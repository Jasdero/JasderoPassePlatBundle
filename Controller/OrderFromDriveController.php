<?php

namespace Jasdero\PassePlatBundle\Controller;


use Google_Service_Drive_DriveFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class OrderFromDriveController extends CheckingController
{


    /**
     * Reading the drive folder sheets and turning it into new orders
     * @Route("/admin/checking/{action}", name="checking")
     * @Method({"GET", "POST"})
     * @param bool $action
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */

    public function scanDriveFolderAction($action = false)
    {
        //retrieving folders parameters
        $newOrdersFolder = $this->get('service_container')->getParameter('jasdero_passe_plat.new_orders_folder');
        $folderToScan = $this->get('service_container')->getParameter('jasdero_passe_plat.folder_to_scan');
        $errorsFolder = $this->get('service_container')->getParameter('jasdero_passe_plat.errors_folder');

        //initializing Client
        $drive = $this->get('jasdero_passe_plat.drive_connection')->connectToDriveApi();
        // getting the files if the OAuth flow has been validated
        $numberOfNewOrders = null;
        $errorsOnOrders = [];
        if ($drive) {
            if ($action) {
                $pageToken = null;

                //scanning the new orders folder to create new orders, then moving it to the In progress folder
                //options to get the new Orders folder on the drive
                $optParamsForFolder = array(
                    'pageToken' => $pageToken,
                    'q' => "name contains '$folderToScan'",
                    'fields' => 'nextPageToken, files(id)'
                );

                //recovering the folder
                $results = $drive->files->listFiles($optParamsForFolder);

                $folderId = '';
                foreach ($results->getFiles() as $file) {
                    $folderId = ($file->getId());
                }

                //options to get the Orders inside the folder
                $optParamsForFiles = array(
                    'pageToken' => $pageToken,
                    'q' => "'$folderId' in parents",
                    'fields' => 'nextPageToken, files(id)'
                );

                //recovering the files
                $results = $drive->files->listFiles($optParamsForFiles);
                $files = [];
                foreach ($results->getFiles() as $file) {
                    $files[] = ($file->getId());
                }
                if ($files) {
                    //downloading files in a csv format and turning it into associative arrays
                    $csvFiles = [];
                    foreach ($files as $file) {
                        $response = $drive->files->export($file, 'text/csv', array(
                            'alt' => 'media',
                        ));
                        $newFile = fopen('order.csv', 'w+');
                        fwrite($newFile, $response->getBody()->getContents());
                        fclose($newFile);

                        //method  to turn csv into arrays
                        $csv = array_map('str_getcsv', file('order.csv'));
                        array_walk($csv, function (&$a) use ($csv) {
                            $a = array_combine($csv[0], $a);
                        });
                        array_shift($csv);
                        $csvFiles[] = $csv;
                    }

                    //formatting csv files to proper order format
                    $newOrders = $this->csvToOrders($csvFiles);

                    //array to store custom Ids and errors which will be added to files later
                    $ordersIds = [];
                    //creating new orders
                    foreach ($newOrders as $newOrder) {
                        //checking order integrity and format
                        if ($user = $this->validateUser($newOrder['user']) && $this->validateOrder($newOrder['products'])) {
                            $numberOfNewOrders++;
                            $ordersIds[] = $this->forward('JasderoPassePlatBundle:Orders:new', array(
                                'user' => $user,
                                'products' => $newOrder['products'],
                                //catching the response with ids
                            ))->getContent();
                        } else {
                            //catching and marking invalid orders
                            $ordersIds[] = 'error';
                            $errorsOnOrders[] = 'Something wrong with order by ' . $newOrder['user'];

                        }
                    }

                    //moving to 'in progress folder' or 'errors' folder

                    //options to get the In Progress folder on the drive
                    $optParamsForFolder = array(
                        'pageToken' => $pageToken,
                        'q' => "name contains '$newOrdersFolder'",
                        'fields' => 'nextPageToken, files(id)'
                    );
                    //recovering the folder id
                    $results = $drive->files->listFiles($optParamsForFolder);
                    $inProgressFolderId = '';
                    foreach ($results->getFiles() as $file) {
                        $inProgressFolderId = ($file->getId());
                    }

                    //options to get the Errors folder on the drive
                    $optParamsForFolder = array(
                        'pageToken' => $pageToken,
                        'q' => "name contains '$errorsFolder'",
                        'fields' => 'nextPageToken, files(id)'
                    );
                    //recovering the folder id
                    $results = $drive->files->listFiles($optParamsForFolder);
                    $errorsFolderId = '';
                    foreach ($results->getFiles() as $file) {
                        $errorsFolderId = ($file->getId());
                    }

                    //moving files
                    foreach ($files as $key => $fileId) {
                        //adding the custom order id as additional property
                        $extraFileMetadata = new Google_Service_Drive_DriveFile(array(
                            "appProperties" => [
                                "customID" => $ordersIds[$key],
                            ]
                        ));
                        // Retrieve the existing parents to remove
                        $file = $drive->files->get($fileId, array('fields' => 'parents'));
                        $previousParents = join(',', $file->parents);

                        //if order has an error move it to errors folder
                        if ($ordersIds[$key] == 'error') {
                            $drive->files->update($fileId, $extraFileMetadata, array(
                                'addParents' => $errorsFolderId,
                                'removeParents' => $previousParents,
                                'fields' => 'id, parents, appProperties'));
                        } else {

                            // Move the file to the in Progress folder
                                 $drive->files->update($fileId, $extraFileMetadata, array(
                                'addParents' => $inProgressFolderId,
                                'removeParents' => $previousParents,
                                'fields' => 'id, parents, appProperties'));
                        }
                    }
                }
            }

            return $this->render('@JasderoPassePlat/Admin/indexAdmin.html.twig', array(
                'newOrders' => $numberOfNewOrders,
                'errors' => $errorsOnOrders
            ));
        } else {

            //if not authenticated restart for token
            return $this->redirectToRoute('auth_checked');

        }
    }


    /**
     * redirection page, used in the OAuth2 authentication Flow
     * @Route("/checked", name="auth_checked")
     *
     */
    public function authCheckedAction()
    {
        return $this->get('jasdero_passe_plat.drive_connection')->authCheckedAction();

    }


    /**
     * used to turn csv files into proper format for new orders
     * @param array $orders
     * @return array
     */
    private function csvToOrders(array $orders)
    {
        $formattedOrders = [];

        foreach ($orders as $order) {
            $formattedOrder = array(
                'user' => '',
                'products' => '',
            );
            foreach ($order as $array) {
                if ($array['user'] !== null) {
                    $formattedOrder['user'] = $array['user'];
                }
                if ($array['products'] !== null) {
                    $formattedOrder['products'][] = (int)$array['products'];
                }
            }
            $formattedOrders[] = $formattedOrder;
        }
        return ($formattedOrders);
    }
}
