<?php
namespace macsch15\capsule\helpers;

class uploads_helper
{
    /**
     * @var \phpbb\user
     */
    protected $user;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(\phpbb\user $user)
    {
        $this->user = $user;
    }

    /**
     * Get uploads error message
     *
     * @param integer $error_number
     * @return string
     */
    public function getUploadsErrorMessage($error_number)
    {
        $message = null;

        switch($error_number) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $message = $this->user->lang('CAPSULE_ERROR_FILE_TOO_LARGE');
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = $this->user->lang('CAPSULE_ERROR_PARTIAL');
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = $this->user->lang('CAPSULE_ERROR_NO_FILE');
                break;
            default:
                $message = $this->user->lang('CAPSULE_ERROR_INTERNAL');
                break;
        }

        return $message;
    }
}
