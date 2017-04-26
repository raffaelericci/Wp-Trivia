<?php

class WpTrivia_Controller_ImportExport extends WpTrivia_Controller_Controller
{

    public function route()
    {

        @set_time_limit(0);
        @ini_set('memory_limit', '128M');

        if (!isset($_GET['action']) || $_GET['action'] != 'import' && $_GET['action'] != 'export') {
            wp_die("Error");
        }

        if ($_GET['action'] == 'export') {
            $this->handleExport();
        } else {
            $this->handleImport();
        }
    }

    private function handleExport()
    {

        if (!current_user_can('wpProQuiz_export')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (isset($this->_post ['exportType']) && $this->_post ['exportType'] == 'xml') {
            $export = new WpTrivia_Helper_ExportXml();
            $filename = 'WpTrivia_export_' . time() . '.xml';
        } else {
            $export = new WpTrivia_Helper_Export();
            $filename = 'WpTrivia_export_' . time() . '.wpq';
        }

        $a = $export->export($this->_post['exportIds']);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo $a;

        exit;
    }

    private function handleImport()
    {

        if (!current_user_can('wpProQuiz_import')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $view = new WpTrivia_View_Import();
        $view->error = false;

        if (isset($_FILES, $_FILES['import']) && substr($_FILES['import']['name'],
                -3) == 'xml' || isset($this->_post['importType']) && $this->_post['importType'] == 'xml'
        ) {
            $import = new WpTrivia_Helper_ImportXml();
            $importType = 'xml';
        } else {
            $import = new WpTrivia_Helper_Import();
            $importType = 'wpq';
        }

        $view->importType = $importType;

        if (isset($_FILES, $_FILES['import']) && $_FILES['import']['error'] == 0) {
            if ($import->setImportFileUpload($_FILES['import']) === false) {
                $view->error = $import->getError();
            } else {
                $data = $import->getImportData();

                if ($data === false) {
                    $view->error = $import->getError();
                }

                $view->import = $data;
                $view->importData = $import->getContent();

                unset($data);
            }
        } else {
            if (isset($this->_post, $this->_post['importSave'])) {
                if ($import->setImportString($this->_post['importData']) === false) {
                    $view->error = $import->getError();
                } else {
                    $ids = isset($this->_post['importItems']) ? $this->_post['importItems'] : false;

                    if ($ids !== false && $import->saveImport($ids) === false) {
                        $view->error = $import->getError();
                    } else {
                        $view->finish = true;
                    }
                }
            } else {
                $view->error = __('File cannot be processed', 'wp-trivia');
            }
        }

        $view->show();
    }
}