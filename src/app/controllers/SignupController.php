<?php

use Phalcon\Mvc\Controller;
use Phalcon\Escaper;

use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;


class SignupController extends Controller
{

    public function IndexAction()
    {
        // defalut action
    }
    // html escapper
    public function registerAction()
    {
        $user = new Users();
        $escaper = new Escaper();
        $data = array(
            "name" => $this->request->getPost("name"),
            "email" => $this->request->getPost("email"),
            "password" => $this->request->getPost("password")
        );
        $data1 = array(
            "name" =>  $this->escaper->escapeHtml($this->request->getPost("name")),
            "email" => $this->escaper->escapeHtml($this->request->getPost("email")),
            "password" => $this->escaper->escapeHtml($this->request->getPost("password"))
        );

        if ($data['name'] == $data1['name'] && $data['email'] == $data1['email'] && $data['password'] == $data1['password']) {
            $user->assign(
                $data,
                [
                    'name',
                    'email',
                    'password',
                ]
            );
            $success = $user->save();
            $this->view->success = $success;
            if ($success) {
                $this->view->message = "Register succesfully";
            } else {
                $this->view->message = "Not Register due to following reason: <br>" . implode("<br>", $user->getMessages());
            }
        } else {
            $adapter = new Stream(APP_PATH . '/log/attack.log');
            $logger  = new Logger(
                'messages',
                [
                    'main' => $adapter,
                ]
            );
            $logger->info("Email is" . $data['email'] . "and pasword is" . $data['password'] . "and name is" . $data['name']);
            $user->assign(
                $data1,
                [
                    'name',
                    'email',
                    'password',
                ]
            );
            $success = $user->save();
            $this->view->success = $success;
            if ($success) {
                $this->view->message = "Register succesfully";
            } else {
                $this->view->message = "Not Register due to following reason: <br>" . implode("<br>", $user->getMessages());
            }
        }
    }
}
