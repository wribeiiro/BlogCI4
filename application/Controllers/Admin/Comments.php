<?php namespace App\Controllers\Admin;

use App\Models\Admin\CommentsModel;

/**
 * Class Comments
 *
 * @package App\Controllers\Admin
 */
class Comments extends Application
{

    /**
     * @var \App\Models\Admin\CommentsModel
     */
    protected $comments_model;

    /**
     * Comments constructor.
     *
     * @param array ...$params
     */
    public function __construct(...$params)
    {
        parent::__construct(...$params);
        $this->comments_model = new CommentsModel();
        $this->stitle = 'Commentaires';
    }

    /**
     * @return \App\Controllers\Admin\Comments
     */
    public function index(): self
    {
        $this->data['count_wait'] = $this->comments_model->countComments('0');
        $this->data['count_ok'] = $this->comments_model->countComments('1');
        $this->data['count_no'] = $this->comments_model->countComments('-1');

        return $this->render('comments/home');
    }

    /**
     * @return \App\Controllers\Admin\Comments
     */
    public function wait(): self
    {
        $this->tpage = 'Commentaires en attentes de validation';
        $this->data['comments'] = $this->comments_model->getComments('0');

        return $this->render('comments/wait');
    }

    /**
     * @return \App\Controllers\Admin\Comments
     */
    public function ok(): self
    {
        $this->tpage = 'Commentaires validés';
        $this->data['comments'] = $this->comments_model->getComments('1');

        return $this->render('comments/ok');
    }

    /**
     * @return \App\Controllers\Admin\Comments
     */
    public function no(): self
    {
        $this->tpage = 'Commentaires refusés';
        $this->data['comments'] = $this->comments_model->getComments('-1');

        return $this->render('comments/no');
    }
}
