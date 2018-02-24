<?php namespace App\Controllers\Admin\Ajax;

use App\Libraries\Mailer;
use App\Models\Admin\ContactModel;

/**
 * Class Contact
 *
 * @package App\Controllers\Admin\Ajax
 */
class Contact extends Ajax
{

    /**
     * @var \App\Models\Admin\ContactModel
     */
    protected $contact_model;

    /**
     * @var \App\Libraries\Mailer
     */
    protected $mailer;

    /**
     * contact constructor.
     *
     * @param array ...$params
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function __construct(...$params)
    {
        parent::__construct(...$params);
        $this->contact_model = new ContactModel();
        $this->mailer = new Mailer();
    }

    /**
     *
     */
    public function rep()
    {
        if ($this->isConnected()) {
            if ($this->csrf->validateToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
                $this->contact_model->markedview($_POST['id']);
                $infocontact = $this->contact_model->getContact($_POST['id']);
                if ($this->mailer->sendmail($_POST['sujet'], $infocontact->email, $_POST['message']) == true) {
                    return $this->responded(['code' => 1, 'title' => 'Contact', 'message' => 'Le message à bien été envoyé']);
                }

                return $this->responded(['code' => 2, 'title' => 'Contact', 'message' => 'Message non envoyé']);
            }
        } else {
            return $this->responded([]);
        }
    }

    /**
     *
     */
    public function markedview()
    {
        if ($this->isConnected()) {
            if ($this->csrf->validateToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
                $this->contact_model->markedview($_POST['id']);

                return $this->responded(['code' => 1, 'title' => 'Contact', 'message' => 'La prise de contact à été marqué comme vue']);
            }
        } else {
            return $this->responded([]);
        }
    }

    /**
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function del()
    {
        if ($this->isConnected()) {
            if ($this->csrf->validateToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
                $this->contact_model->del($_POST['id']);

                return $this->responded(['code' => 1, 'title' => 'Contact', 'message' => 'La prise de contact à été supprimé']);
            }
        } else {
            return $this->responded([]);
        }
    }
}
