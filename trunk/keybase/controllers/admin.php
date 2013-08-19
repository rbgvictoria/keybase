<?php
/**
 * @property CI_Loader $load
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Email $email
 * @property CI_DB_active_record $db
 * @property CI_DB_forge $dbforge
 */

class Admin extends CI_Controller {
// a class to allow users to log in and set a session var to say they
// are logged in.
    var $data;
    
    function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('captcha');
        $this->output->enable_profiler(true);
        $this->load->model('authenticationmodel');
    }

    function index($message="") {
        $this->login($message);
    }
    
    function login($message="") {
        if (isset($_SERVER['HTTP_REFERER']) && substr($_SERVER['HTTP_REFERER'], 0, strlen(base_url())) == base_url())
            $this->data['referer'] = $_SERVER['HTTP_REFERER'];
        else
            $this->data['referer'] = FALSE;
        $this->load->view('login', $this->data);
    }

    function authenticate(){
    // do the authenticate stuff here:
    if($this->input->post('username') && $this->input->post('passwd')){
                    if($this->authenticationmodel->checkLogin())
            if ($this->input->post('referer')){
                redirect($this->input->post('referer'));
            }
            else 
                redirect('key');
        else $message = 'Authentication failed';
        $this->load->view('message', array("message" => $message));
    }
    else 
        $this->load->view('message', array('message' => "Username or password not filled in"));
    }

    function logout(){
        // unset the session variables, then destroy the session
        $unset = array('id'=>'', 'name'=>'', 'firstname'=>'', 'surname'=>'', 'email'=>'', 'role'=>'');
        $this->session->unset_userdata($unset);
        //$this->session->sess_destroy();
        if (isset($_SERVER['HTTP_REFERER']) && substr($_SERVER['HTTP_REFERER'], 0, strlen(base_url())) == base_url()) 
            redirect($_SERVER['HTTP_REFERER']);
        else 
            redirect('/key');
    }
    
    function register() {
        $this->data['js'][] = base_url() . 'js/jquery.keybase.email.js';
        if ($this->input->post('submit')) {
            if ($this->input->post('firstname') && $this->input->post('lastname') && $this->input->post('email') &&
                    $this->input->post('username') && $this->input->post('passwd') && $this->input->post('confirm')) {
                if ($this->input->post('confirm') == $this->input->post('passwd')) {
                    if(!$this->authenticationmodel->checkUsername($this->input->post('username'))) {
                        $captcha_valid = $this->authenticationmodel->validateCaptcha($this->input->post('captcha'));
                        if ($captcha_valid) {
                            $this->authenticationmodel->createAccount($this->input->post());
                            $this->data['success'] = TRUE;
                        }
                        else
                            $this->data['messages'][] = 'CAPTCHA is incorrect.';
                    }
                    else 
                        $this->data['messages'][] = 'The username you entered is already in use. Please enter a different username.';
                }
                else {
                    $this->data['messages'][] = 'Password and confirmation don&apos;t match';
                }
            }
            else {
                $this->data['messages'][] = 'Please fill in all required fields.';
            }
            
        }
        
        $this->data['captcha'] = $this->captcha();
        
        $this->load->view('registrationview', $this->data);
    }
    
    public function captcha() {
        $vals = array(
            'word' => $this->authenticationmodel->getCaptchaWord(),
            'img_path' => './captcha/',
            'img_url' => base_url() . 'captcha/',
            'img_width' => 200,
            'img_height' => 30
        );
        $captcha = create_captcha($vals);
        $this->authenticationmodel->storeCaptcha($captcha);
        return $captcha;
    }
}
?>
