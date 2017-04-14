<?php


namespace Jasdero\PassePlatBundle\Services;


use Doctrine\ORM\EntityManager;
use Google_Service_Drive_DriveFile;

/**
 * Class DriveFolderAsStatus
 * @package Jasdero\PassePlatBundle\Services
 * Used to move files to a drive folder named after the order's status
 */
class DriveFolderAsStatus
{
    private $drive;
    private $em;
    private $rootFolder;

    /**
     * DriveFolderAsStatus constructor.
     * @param DriveConnection $drive
     * @param EntityManager $em
     * @param $rootFolder
     */
    public function __construct(DriveConnection $drive, EntityManager $em, $rootFolder)
    {
        $this->drive = $drive;
        $this->em = $em;
        $this->rootFolder = $rootFolder;
    }

    /**
     * @param $statusName
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function driveFolder($statusName, $orderId)
    {
        //initializing Client
        $drive = $this->drive->connectToDriveApi();


        // getting to work if the OAuth flow has been validated
        if ($drive) {

            //getting the id of root folder
            $pageToken = null;
            $optParamsForFolder = array(
                'pageToken' => $pageToken,
                'q' => "name contains '$this->rootFolder'",
                'fields' => 'nextPageToken, files(id)'
            );

            //recovering the folder
            $results = $drive->files->listFiles($optParamsForFolder);

            $rootFolderId = '';
            foreach ($results->getFiles() as $file) {
                $rootFolderId = ($file->getId());
            }

            //checking if the folder with status name already exists
            $optParamsForFolder = array(
                'pageToken' => $pageToken,
                'q' => "name contains '$statusName'",
                'fields' => 'nextPageToken, files(id)'
            );

            $results = $drive->files->listFiles($optParamsForFolder);
            $folderId = '';
            foreach ($results->getFiles() as $file) {
                $folderId = ($file->getId());
            }
            //creating folder if it doesn't exist
            if (!$folderId) {
                $fileMetadata = new Google_Service_Drive_DriveFile(array(
                    'name' => $statusName,
                    'parents' => array($rootFolderId),
                    'mimeType' => 'application/vnd.google-apps.folder'));
                $file = $drive->files->create($fileMetadata, array(
                    'uploadType' => 'multipart',
                    'fields' => 'id'));
                $folderId = ($file->getId());
            } else {
                //if exists getting id
                foreach ($results->getFiles() as $file) {
                    $folderId = ($file->getId());
                }
            }

            //checking the file exists

            //retrieving file corresponding to order
            $optParamsForFile = array(
                'pageToken' => $pageToken,
                'q' => "appProperties has { key = 'customID' and value = '$orderId'}",
                'fields' => 'nextPageToken, files(id)'
            );

            //recovering the file id
            $results = $drive->files->listFiles($optParamsForFile);
            $fileId = '';
            foreach ($results->getFiles() as $file) {
                $fileId = ($file->getId());
            }

            //if file exists
            if ($fileId) {
                //moving file
                $emptyFileMetadata = new Google_Service_Drive_DriveFile();
                // Retrieve the existing parents to remove
                $file = $drive->files->get($fileId, array('fields' => 'parents'));
                $previousParents = join(',', $file->parents);

                // Move the file to the new folder
                $drive->files->update($fileId, $emptyFileMetadata, array(
                    'addParents' => $folderId,
                    'removeParents' => $previousParents,
                    'fields' => 'id, parents'));

                //else need to create it
            } else {
                $orderAsCsv = [];
                $completeOrder = $this->em->getRepository('JasderoPassePlatBundle:Product')->findBy(['orders' => $orderId]);

                //formatting order to create csv file
                foreach ($completeOrder as $key => $order) {
                    $orderAsCsv[0][0] = 'user';
                    $orderAsCsv[0][1] = 'products';
                    $orderAsCsv[0][2] = 'comments';
                    $orderAsCsv[$key + 1][] = $order->getOrders()->getUser()->getEmail();
                    $orderAsCsv[$key + 1][] = $order->getCatalog()->getId();
                    if($order->getOrders()->getComment()){
                        $orderAsCsv[$key + 1][] = $order->getOrders()->getComment();
                    } else {
                        $orderAsCsv[$key + 1][] = 'no comments';
                    }
                }
                $newFile = fopen('orderToCsv.csv', 'w+');
                foreach ($orderAsCsv as $files) {
                    fputcsv($newFile, $files);
                }
                fclose($newFile);

                //getting data to create file on drive
                $data = file_get_contents('orderToCsv.csv');

                $fileMetadata = new Google_Service_Drive_DriveFile(array(
                    'name' => 'Custom Order '.$orderId,
                    'mimeType' => 'application/vnd.google-apps.spreadsheet',
                    'parents' => array($folderId),
                    "appProperties" => [
                        "customID" => $orderId,
                    ]));
                $drive->files->create($fileMetadata, array(
                    'data' => $data,
                    'mimeType' => 'text/csv',
                    'uploadType' => 'multipart',
                    'fields' => 'id, parents, appProperties'));
            }

            //setting order as synchronized
            $order = $this->em->getRepository('JasderoPassePlatBundle:Orders')->find($orderId);
            $order->setDriveSynchro(true);
            $this->em->flush();

        } else {
            //if not authenticated restart for token
            return $this->drive->authCheckedAction();
        }
    }

}
