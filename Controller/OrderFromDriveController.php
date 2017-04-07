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
     * @Route("/admin/checking/", name="checking")
     * @Method({"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */

    public function scanDriveFolderAction()
    {
        //retrieving folders parameters
        $container = $this->get('service_container');
        $newOrdersFolder = $container->getParameter('new_orders_folder');
        $folderToScan = $container->getParameter('folder_to_scan');
        $errorsFolder = $container->getParameter('errors_folder');

        //initializing Client
        $drive = $this->get('jasdero_passe_plat.drive_connection')->connectToDriveApi();

        // vars to display when action triggered
        $numberOfNewOrders = 0;
        $errorsOnOrders = 0;

        // getting the files if the OAuth flow has been validated
        if ($drive) {

                // getting new files
                $folderId = $this->findDriveFolder($drive, $folderToScan);
                $files = $this->getFilesFromFolder($drive, $folderId);

                if ($files) {
                    //downloading files in a csv format and turning it into associative arrays
                    $csvFiles = $this->downloadAndConvert($drive, $files);

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
                                'comments' => $newOrder['comments'],
                                //catching the response with ids
                            ))->getContent();
                        } else {
                            //catching and marking invalid orders
                            $ordersIds[] = 'error';
                            $errorsOnOrders++;
                        }
                    }

                    //moving to 'in progress folder' or 'errors' folder
                    // getting the In Progress folder on the drive
                    $inProgressFolderId = $this->findDriveFolder($drive, $newOrdersFolder);

                    // getting the Errors folder on the drive
                    $errorsFolderId = $this->findDriveFolder($drive, $errorsFolder);

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
            } elseif (!$drive){

            return $this->redirectToRoute('auth_checked');
        }

        return $this->redirectToRoute('drive_index', array(
            'errors' => $errorsOnOrders,
            'newOrders' => $numberOfNewOrders
        ));
    }

    /**
     * @param $drive
     * @param $folder
     * @return string
     */
    private function findDriveFolder($drive, $folder)
    {
        $pageToken = null;

        $optParamsForFolder = array(
            'pageToken' => $pageToken,
            'q' => "name contains '$folder'",
            'fields' => 'nextPageToken, files(id)'
        );
        //recovering the folder
        $results = $drive->files->listFiles($optParamsForFolder);

        $folderId = '';
        foreach ($results->getFiles() as $file) {
            $folderId = ($file->getId());
        }
        return $folderId;
    }

    /**
     * @param $drive
     * @param $folder
     * @return array
     */
    private function getFilesFromFolder($drive, $folder)
    {
        $pageToken = null;

        //options to get the Orders inside the folder
        $optParamsForFiles = array(
            'pageToken' => $pageToken,
            'q' => "'$folder' in parents",
            'fields' => 'nextPageToken, files(id)'
        );

        //recovering the files
        $results = $drive->files->listFiles($optParamsForFiles);
        $files = [];
        foreach ($results->getFiles() as $file) {
            $files[] = ($file->getId());
        }

        return $files;
    }

    /**
     * @param $drive
     * @param array $files
     * @return array
     */
    private function downloadAndConvert($drive, array $files)
    {
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

        return $csvFiles;
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
                'comments' => '',
            );
            foreach ($order as $array) {
                if ($array['user'] !== '') {
                    $formattedOrder['user'] = $array['user'];
                }
                if ($array['products'] !== null) {
                    $formattedOrder['products'][] = (int)$array['products'];
                }
                if (isset($array['comments']) && $array['comments'] !== '') {
                    $formattedOrder['comments'] = $array['comments'];
                }
            }
            $formattedOrders[] = $formattedOrder;
        }
        return ($formattedOrders);
    }
}
