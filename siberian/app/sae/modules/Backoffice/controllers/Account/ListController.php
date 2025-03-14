<?php

/**
 * Class Backoffice_Account_ListController
 */
class Backoffice_Account_ListController extends Backoffice_Controller_Default
{

    public function loadAction()
    {
        $payload = [
            "title" => sprintf('%s > %s > %s',
                __('Manage'),
                __('Backoffice access'),
                __('Accounts')),
            "icon" => "fa-users",
        ];

        $this->_sendJson($payload);
    }

    public function findallAction()
    {

        $user = new Backoffice_Model_User();
        $users = $user->findAll();
        $data = ["users" => []];

        foreach ($users as $user) {
            $data["users"][] = [
                "id" => $user->getId(),
                "email" => $user->getEmail(),
                "created_at" => $user->getFormattedCreatedAt($this->_("MM/dd/yyyy"))
            ];
        }
        $this->_sendJson($data);
    }

    public function deleteAction()
    {

        if ($data = Zend_Json::decode($this->getRequest()->getRawBody())) {

            try {

                if (__getConfig('is_demo')) {
                    // Demo version
                    throw new Exception("This is a demo version, this user can't be deleted");
                }

                if ((new Backoffice_Model_User())->findAll()->count() <= 1) {
                    throw new Exception(__("How do you want to access the backoffice if you remove the only user remaining?"));
                }

                if (empty($data["user_id"])) {
                    throw new Exception(__("An error occurred while saving. Please try again later."));
                }

                $user = new Backoffice_Model_User();
                $user->find($data["user_id"]);
                if (!$user->getId()) {
                    throw new Exception(__("An error occurred while saving. Please try again later."));
                }

                $user->delete();

                $data = [
                    "success" => 1,
                    "message" => __("User successfully deleted")
                ];
            } catch (Exception $e) {
                $data = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }

            $this->_sendJson($data);

        }

    }

}
