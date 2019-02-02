<?php
namespace macsch15\capsule\controller;

use macsch15\capsule\helpers\image_helper;
use macsch15\capsule\helpers\uploads_helper;

class controller
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\pagination */
    protected $pagination;

    /** @var \phpbb\controller\helper */
    protected $helper;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var string phpBB root path */
    protected $root_path;

    /** @var string phpEx */
    protected $php_ext;

    /** @var string Prefix */
    protected $table_prefix;

    /** @var macsch15\capsule\helpers\image_helper */
    protected $image;

    /** @var macsch15\capsule\helpers\uploads_helper */
    protected $uploads;

    /** @var string Unique Id */
    protected $unique_id;

    /** @var integer All images */
    protected $images_count;

    const UPLOAD_FOLDER = 'hosting';
    const ALLOWED_MIME = ['image/gif', 'image/jpeg', 'image/png'];
    const ALLOWED_EXTENSION = ['.jpeg', '.png', '.gif', '.jpg', '.JPEG', '.PNG', '.GIF', '.JPG'];
    const MAX_SIZE = 5242880;
    const IMAGE_NAME_REMOVE_REGEX = '/[^A-Za-z0-9_\-\.]/i';
    const IMAGES_PER_PAGE = 5;

    /**
    * Constructor
    * 
    * @param \phpbb\config\config $config
    * @param \phpbb\request\request $request
    * @param \phpbb\db\driver\driver_interface $db
    * @param \phpbb\pagination $pagination
    * @param \phpbb\template\template $template
    * @param \phpbb\controller\helper $helper
    * @param \phpbb\user $user
    * @param string $root_path
    * @param string $php_ext
    * @param string $table_prefix
    * @return void
    */
    public function __construct(
        \phpbb\config\config $config,
        \phpbb\request\request $request,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\pagination $pagination,
        \phpbb\template\template $template,
        \phpbb\controller\helper $helper,
        \phpbb\user $user,
        $root_path,
        $php_ext,
        $table_prefix
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->db = $db;
        $this->pagination = $pagination;
        $this->template = $template;
        $this->helper = $helper;
        $this->user = $user;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;
        $this->image = new image_helper($this->user);
        $this->uploads = new uploads_helper($this->user);
        $this->unique_id = time() . '_';
    }

    /**
    * Display the index page
    *
    * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
    */
    public function display()
    {
        $this->user->add_lang_ext('macsch15/capsule', 'capsule');

        page_header($this->user->lang('CAPSULE_TITLE'));
        add_form_key('capsule-form');

        $this->checkIfUserIsLoggedIn();

        $start = $this->request->variable('start', 0); 

        $sql = 'SELECT * 
            FROM ' . $this->table_prefix . 'capsule 
            WHERE img_user_id = ' . (int) $this->user->data['user_id'] . ' 
            ORDER BY img_id DESC';
        $result = $this->db->sql_query_limit($sql, 10, $start);

        while ($row = $this->db->sql_fetchrow($result)) {
            $this->template->assign_block_vars('images', array(
                'IMG_ID' => $row['img_id'],
                'IMG_NAME' => $row['img_filename'],
                'IMG_TIME' => $this->user->format_date($row['img_uploaded_time']),
                'IMG_SIZE' => round($row['img_size'] / 1024)
            ));
        }

        $this->addPagination($sql, $start);

        $this->template->assign_block_vars('navlinks', [
            'FORUM_NAME' => $this->user->lang('CAPSULE_TITLE'),
            'U_VIEW_FORUM' => $this->helper->route('macsch15_capsule_main_controller')
        ]);

        $this->template->assign_vars([
            'ALL_IMAGES_COUNT' => $this->countAllImages(),
            'IMG_FOLDER' => self::UPLOAD_FOLDER,
            'FORM_ROUTE' => $this->helper->route('macsch15_capsule_main_controller_action')
        ]);

        return $this->helper->render('index.html');
    }

    /**
    * Do magic.
    *
    * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
    */
    public function process()
    {
        $this->user->add_lang_ext('macsch15/capsule', 'capsule');
        page_header($this->user->lang('CAPSULE_TITLE'));

        $this->checkCsrfToken();
        $this->checkIfImageMimeIsAllowed();
        $this->checkIfImageExtensionIsAllowed();
        $this->validateImageSize();
        $this->makeDestinationDirectoryIfNotExists();
        $this->checkIfImageAlreadyExists();
        $this->moveUploadedImage();
        $this->buildThumbnailForUploadedImage();
        $this->createNewEntityForImage();

        $this->template->assign_block_vars('navlinks', [
            'FORUM_NAME' => $this->user->lang('CAPSULE_TITLE'),
            'U_VIEW_FORUM' => $this->helper->route('macsch15_capsule_main_controller')
        ]);
        
        $this->template->assign_vars(array(
            'IMG_FOLDER' => self::UPLOAD_FOLDER,
            'IMG_NAME' => $this->getImageFilename()
        ));

        return $this->helper->render('overview.html');
    }

    /**
     * Check if user is logged in
     *
     * @throws \Exception
     * @return void
     */
    protected function checkIfUserIsLoggedIn()
    {
        if ($this->user->data['username'] === 'Anonymous') {
            trigger_error($this->user->lang('LOGIN_REQUIRED'));
        }
    }

    /**
     * Validate CSRF token
     *
     * @throws \Exception
     * @return void
     */
    protected function checkCsrfToken()
    {
        if (!check_form_key('capsule-form')) {
            trigger_error($this->user->lang('FORM_INVALID'));
        }
    }

    /**
     * Check if image mime is allowed
     *
     * @throws \Exception
     * @return void
     */
    protected function checkIfImageMimeIsAllowed()
    {
        if ($this->request->file('img')['error'] != 0) {
            trigger_error($this->uploads->getUploadsErrorMessage($this->request->file('img')['error']));
        }

        if (in_array($this->image->getMime($this->request->file('img')['tmp_name']), self::ALLOWED_MIME, true) === false) {
            trigger_error($this->user->lang('CAPSULE_ERROR_MIME'));
        }        
    }

    /**
     * Check if image extension is allowed
     *
     * @throws \Exception
     * @return void
     */
    protected function checkIfImageExtensionIsAllowed()
    {
        $extension = substr($this->request->file('img')['name'], strrpos($this->request->file('img')['name'], '.'));

        if (in_array($extension, self::ALLOWED_EXTENSION, true) === false) {
            trigger_error($this->user->lang('CAPSULE_ERROR_MIME'));
        }
    }

    /**
     * Validate image size
     *
     * @throws \Exception
     * @return void
     */
    protected function validateImageSize()
    {
        if ((bool) @getimagesize($this->request->file('img')['tmp_name']) === false) {
            trigger_error($this->user->lang('FORM_INVALID'));
        }

        if ($this->request->file('img')['size'] > self::MAX_SIZE) {
            trigger_error($this->user->lang('CAPSULE_ERROR_SIZE'));
        }   
    }

    /**
     * Get destination directory
     *
     * @return string
     */
    protected function getDestinationDirectory()
    {
        return $this->root_path . '/' . self::UPLOAD_FOLDER . '/';
    }

    /**
     * Get image filename
     *
     * @return string
     */
    protected function getImageFilename()
    {
        $filename = preg_replace(self::IMAGE_NAME_REMOVE_REGEX, '', $this->request->file('img')['name']);
        $filename = $this->unique_id . '_' . $filename;

        return $filename;
    }

    /**
     * Check if image already exists
     *
     * @throws \Exception
     * @return void
     */
    protected function checkIfImageAlreadyExists()
    {
        if (file_exists($this->getDestinationDirectory() . $this->getImageFilename()) === true) {
            trigger_error($this->user->lang('CAPSULE_ERROR_GENERAL'));
        }   
    }

    /**
     * Make destination directory
     *
     * @return void
     */
    protected function makeDestinationDirectoryIfNotExists()
    {
        if (is_dir($this->getDestinationDirectory()) === false) {
            mkdir($this->getDestinationDirectory(), 0777);
        }        
    }

    /**
     * Move uploaded image
     *
     * @return void
     */
    protected function moveUploadedImage()
    {
        move_uploaded_file($this->request->file('img')['tmp_name'], $this->getDestinationDirectory() . $this->getImageFilename());
    }

    /**
     * Build thumbnail for image
     *
     * @return void
     */
    protected function buildThumbnailForUploadedImage()
    {
        $this->image->makeThumbnail($this->getDestinationDirectory(), $this->getImageFilename(), 'thumb_');        
    }

    /**
     * Save image entity in DB
     *
     * @return void
     */
    protected function createNewEntityForImage()
    {
        $data['img_user_id'] = $this->user->data['user_id'];
        $data['img_filename'] = $this->getImageFilename();
        $data['img_uploaded_time'] = time();
        $data['img_size'] = $this->request->file('img')['size'];

        $sql = 'INSERT INTO ' . $this->table_prefix . 'capsule ' . $this->db->sql_build_array('INSERT', $data);

        $this->db->sql_query($sql);
    }

    /**
     * Set images couter
     *
     * @param integer $images
     * @return void
     */
    protected function setImagesCounter($images)
    {
        $this->images_count = $images;
    }

    /**
     * Count all images
     *
     * @return integer
     */
    protected function countAllImages()
    {
        return (int) $this->images_count;
    }

    /**
     * Add pagination
     *
     * @param string $sql
     * @param integer $start
     * @return void
     */
    protected function addPagination($sql, $start)
    {
        $query_result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrowset($query_result);

        $total_count = (int) count($row);

        $start = $this->pagination->validate_start($start, self::IMAGES_PER_PAGE, $total_count);
        $this->pagination->generate_template_pagination($this->helper->route('macsch15_capsule_main_controller'), 'pagination', 'start', $total_count, self::IMAGES_PER_PAGE, $start);

        $this->setImagesCounter($total_count);
    }
}
