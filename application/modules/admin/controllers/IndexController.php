<?php

class Admin_IndexController extends Zend_Controller_Action {

    private $_flashMessenger;

    public function init() {
        $this->_flashMessenger = $this->_helper->FlashMessenger;

        $flashMessenger = $this->_flashMessenger->getMessages();
        if (!empty($flashMessenger)) {
            $currentMessage = current($flashMessenger);
            if (!empty($currentMessage['info'])) {
                $this->view->infoFlashMessage = $currentMessage['info'];
            } else if (!empty($currentMessage['error'])) {
                $this->view->errorFlashMessage = $currentMessage['error'];
            }
        }
    }

    public function indexAction() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect("/admin/index/login");
        }
    }

    public function loginAction() {
        $auth = Zend_Auth::getInstance();

        if (!$auth->hasIdentity()) {
            $form = new Admin_Form_LoginForm();
            $this->view->form = $form;
        } else {
            $this->view->authInfo = $auth->getIdentity();
        }

        //db adapter
        $userDbAdapter = Admin_Model_DbTable_Admin::getDefaultAdapter();

        //overeni, zda byl formular odeslan a zda je validni
        if ($this->getRequest()->isPost()
                && $form->isValid($this->getRequest()->getPost())) {
            //vytvoreni auth adapteru
            $adapter = new Zend_Auth_Adapter_DbTable($userDbAdapter, 'admin');

            //pridany podminky prihlaseni - podle username, user_password a user_type
            $adapter->setIdentityColumn('admin_email')->setCredentialColumn('admin_password');
            $password = Admin_Model_Admin::SALT . $form->getValue('admin_password');
            $adapter->setIdentity($form->getValue('admin_email'))->setCredential($password);
            $adapter->setCredentialTreatment('SHA1(CONCAT(?, admin_salt)) AND admin_is_active=1');

//            print_r($adapter);die;
            //vytvorim instanci Zend_Auth a provedu autentizaci

            $result = $auth->authenticate($adapter);

//            print_r($result);die;
            //postup po autentizaci prihlasit/neprihlasit
            if (!$result->isValid()) {
                //print_r($result->getMessages());
                foreach ($result->getMessages() as $message) {
                    echo $message;
                }
            } else {
                $storage = $auth->getStorage();
                $storage->write(
                        $adapter->getResultRowObject(
                                null, array('password')
                        )
                );
                $this->_flashMessenger->addMessage(array('info' => 'Přihlášení proběhlo úspěšně'));
                $this->_redirect("/admin");
            }
        }
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_flashMessenger->addMessage(array('info' => 'Odhlášení proběhlo úspěšně'));
        $this->_redirect("/admin/index/login");
    }

    public function registrationAction() {
        $form = new Admin_Form_RegistrationForm();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $adminMapper = new Admin_Model_AdminMapper();
                $admin = new Admin_Model_Admin();
                $admin->setOptions($form->getValues());
                $adminMapper->save($admin);
                $this->_flashMessenger->addMessage(array('info' => 'Registrace proběhla úspěšně. Nyní musíte vyčkat na aktivaci Vašeho účtu.'));
                $this->_redirect("admin/index/registration");
            } else {
                $form->populate($this->getRequest()->getParams());
            }
        }
    }

}

