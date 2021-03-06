<?php

  class BProfilec extends CI_Controller{

    public function __construct(){
      parent::__construct();

      //prevent user if not logged in into the system
      if($this->session->userdata('logged_in') != true ){
        redirect(base_url(), '');
      }
      if($this->session->userdata('user_type') != 'user' ){
        redirect(base_url(), '');
      }
    }


    public function create_profile(){
      //create profile if not exist
      $user_id = $this->session->userdata('buyer_id');
      $sql = "select * from userprofile where user_id = ". $user_id;
      $data = $this->db->query($sql)->result_array();
      if($data){
        redirect(base_url() . "profile");
      }
      else{
        $this->load->view('buyer/profile/buyer_create');
      }
    }


    function fetch(){
      $this->load->model('autocomplete_model');
      echo $this->autocomplete_model->fetch_data($this->uri->segment(4));
    }

    //View Profile CODE START
    function view_profile(){
        $user_id = $this->session->userdata('buyer_id');
      $sql = "select * from userprofile where user_id = ". $user_id;
      $data = $this->db->query($sql)->result_array();
      if($data){
          $this->load->view('buyer/includes/buyer_VPheader');
          $this->load->view('buyer/includes/buyer_serviceBar');
          $this->load->view('buyer/profile/buyer_view');
          $this->load->view('buyer/includes/buyer_footer');
          $this->load->view('buyer/includes/buyer_end');
      }
      else{
        $this->load->view('buyer/profile/buyer_create');
      }
    }
    //View Profile CODE END

    //Update Profile CODE START
    function edit_profile(){
      $this->load->view('buyer/includes/buyer_VPheader');
      $this->load->view('buyer/includes/buyer_serviceBar');
      $this->load->view('buyer/profile/buyer_update');
      $this->load->view('buyer/includes/buyer_footer');
      $this->load->view('buyer/includes/buyer_end');
    }
    //Update Profile CODE END

    //create profile code start

   //DO UPLOAD FUNCTION CODE START
   function do_upload(){
      $config['upload_path']   = './images/original/';
	  	$config['allowed_types'] = 'gif|jpg|png|jpeg';
		  $config['max_size']      = '5120';
		  $config['max_height'] 	 = '10240';
	  	$config['max_height']    = '10240';
	  	$config['encrypt_name']  = true;
	  	$this->load->library('upload', $config);
	   	// Alternately you can set preferences by calling the ``initialize()`` method. Useful if you auto-load the class:
	  	$this->upload->initialize($config);
	  	if ( ! $this->upload->do_upload('pic')){
        #$this->session->set_flashdata('error', $this->upload->display_errors());
        $error['error'] = $this->upload->display_errors();
        return $error;
	  	}else{
	  	 $data = $this->upload->data();
	  	 $file_name = $data['file_name'];
       $this->resizeImage($file_name);
       return $file_name;
      }
    }
   //DO UPLOAD FUNCTION CODE END

   function resizeImage($filename){
      $source_path = 'images/original/' . strval($filename);
      $resized_path = 'images/resized/';
      $thumb_path = 'images/thumbnail/';
      $config_manip = array(
          'image_library' => 'gd2',
          'source_image' => $source_path,
          'new_image' => $resized_path,
          'maintain_ratio' => TRUE,
          'width' => 256,
          'height' => 256
      );
      $this->load->library('image_lib');
      $this->image_lib->initialize($config_manip);
      $this->image_lib->resize();

      $config_manip = array(
          'image_library' => 'gd2',
          'source_image' => $source_path,
          'new_image' => $thumb_path,
          'maintain_ratio' => TRUE,
          'width' => 128,
          'height' => 128
      );
      $this->image_lib->initialize($config_manip);
      $this->image_lib->resize();

      $this->image_lib->clear();
      return true;
   }
    //DO UPLOAD FUNCTION CODE END

    public function createuserprofile(){

      //languages code end
      if($this->input->post('language1')){
        $language = array(
          'user_id'   => $this->session->userdata('buyer_id'),
          'language'  => $this->input->post('language1'),
          'level'     => $this->input->post('llevel1'),
        );
        if($language){
          $this->db->insert('languages', $language);
        }
      }
      if($this->input->post('language2')){
        $language = array(
          'user_id' => $this->session->userdata('buyer_id'),
          'language' => $this->input->post('language2'),
          'level' => $this->input->post('llevel2')
        );
        if($language){
          $this->db->insert('languages', $language);
        }
      }
      if($this->input->post('language3')){
        $language = array(
          'user_id' => $this->session->userdata('buyer_id'),
          'language' => $this->input->post('language3'),
          'level' => $this->input->post('llevel3')
        );
        if($language){
          $this->db->insert('languages', $language);
        }
      }
      //languages code end

      //School code Start
      if($this->input->post('school_name') != ''){
          $school_data ['country'] = $this->input->post('school_country');
          $school_data ['state'] = $this->input->post('school_state');
          $school_data ['city'] = $this->input->post('school_city');
          $school_data ['school_name'] = $this->input->post('school_name');
          $school_data ['year'] = $this->input->post('year');
          $school_data ['user_id'] = $this->session->userdata('buyer_id');
          $this->load->model('aschool_model');
          $this->aschool_model->insert_userschool($school_data);
      }
      //School Code End

      //profile create code start

      if($this->input->post('country') != ''){
          $data ['country'] = $this->input->post('country');
      }

      $pic = $this->do_upload();
      if(isset($pic['error'])){
        #echo $pic['error'];
        $this->session->set_flashdata('error', $pic['error']);
        redirect(base_url('create_profile'));
      }else{
        $data['picture'] = $pic;
      }

      $data ['description'] = $this->input->post('description');
      $data ['gender'] = $this->input->post('gender');
      $data ['user_id'] = $this->session->userdata('buyer_id');
      $data ['join_date'] = date("Y/m/d");
      $this->load->model('profile_model');
      if($this->profile_model->insert_profile($data)){
        redirect(base_url('profile'));
      }
      else{
        $this->session->set_flashdata('error', 'Something occured! Please try again.');
        redirect(base_url('create_profile'));
      }// Redirect to Success page
      //profile create code end
    }

    //edit profile code START
    function edituserprofile(){
      //languages code end
      if($this->input->post('language1')){
        $language = array(
          'user_id' => $this->session->userdata('buyer_id'),
          'language' => $this->input->post('language1'),
          'level' => $this->input->post('llevel1')
        );
        if($language){
          $this->db->insert('languages', $language);
        }
      }
      if($this->input->post('language2')){
        $language = array(
          'user_id' => $this->session->userdata('buyer_id'),
          'language' => $this->input->post('language2'),
          'level' => $this->input->post('llevel2')
        );
        if($language){
          $this->db->insert('languages', $language);
        }
      }
      if($this->input->post('language3')){
        $language = array(
          'user_id' => $this->session->userdata('buyer_id'),
          'language' => $this->input->post('language3'),
          'level' => $this->input->post('llevel3')
        );
        if($language){
          $this->db->insert('languages', $language);
        }
      }
      //languages code end

      //profile create code start
      $data ['description'] = $this->input->post('description');
  		$data ['qualification'] = $this->input->post('degreetitle');
      if($data){
        $this->db->where('user_id', $this->session->userdata('buyer_id'));
        $this->db->update('userprofile', $data);
        $this->session->set_flashdata('success', 'Profile updated');
        redirect(base_url('edit_profile'));
      }else{
        $this->session->set_flashdata('error', 'Something occured! Please try again.');
        redirect(base_url('edit_profile'));
      }// Redirect to Success page
    }
    //edit profile code end

    //edit profile code START
    function updatepic(){
      //profile create code start
      $pic = $this->do_upload();
      if(isset($pic['error'])){
        #echo $pic['error'];
        $this->session->set_flashdata('error', $pic['error']);
        redirect(base_url('edit_profile'));
      }else{
        $data ['picture'] = $pic;
        $this->db->where('user_id', $this->session->userdata('buyer_id'));
        if($this->db->update('userprofile', $data)){
          $this->session->set_flashdata('success', 'Profile updated');
          redirect(base_url('edit_profile'));
        }
      }
    }
    //edit profile code end

    //Update School code START
    function update_school(){
      $data['school_id'] = $this->input->post('school_id');
      $data['country'] = $this->input->post('country');
      $data['state'] = $this->input->post('state');
      $data['city'] = $this->input->post('city');
      $data['user_id'] = $this->input->post('user_id');
      $data['school_name'] = $this->input->post('school_name');
      $this->load->model('Aschool_model');
      $this->Aschool_model->update_school($data);
      redirect(base_url(). 'profile');
    }
    //Update School code END

    //LOGOUT FUNCTION CODE START
    public function logout(){
      $this->session->sess_destroy();
      redirect(base_url() . "");
    }
    //LOGOUT FUNCTION CODE END

    //Add User School Code Start
    function add_school(){
      $school_data ['country'] = $this->input->post('country');
      $school_data ['state'] = $this->input->post('state');
      $school_data ['city'] = $this->input->post('city');
      $school_data ['school_name'] = $this->input->post('school_name');
      $school_data ['year'] = $this->input->post('year');
      $school_data ['user_id'] = $this->session->userdata('buyer_id');
      $this->load->model('aschool_model');
      $this->aschool_model->insert_userschool($school_data);
      redirect(base_url() . "profile");
    }
    //Add User School Code End

    //Delete User School Code End
    function delete_userschool(){
      $this->load->model('aschool_model');
      $school_id = $this->input->post('userschool_id');
      $this->aschool_model->delete_userschool($school_id);
      redirect(base_url(). 'profile');
    }
    //Delete User School Code End

    //PAGE SETTINGS CODE START
    function settings(){
      $this->load->view('buyer/includes/buyer_VPheader');
      $this->load->view('buyer/includes/buyer_serviceBar');
      $this->load->view('buyer/profile/settings');
      $this->load->view('buyer/includes/buyer_footer');
      $this->load->view('buyer/includes/buyer_end');
    }
    //PAGE SETTINGS CODE END

    //CHANGE PASSWORD CODE START
    function changepassword(){
      $this->form_validation->set_rules("currentpassword", "Password", "trim|required|min_length[8]");
  		$this->form_validation->set_rules("newpassword", "Password", "trim|required|min_length[8]");
  		$this->form_validation->set_rules("newpasswordconfirm", "Password Confirmation", "trim|required|matches[newpasswordconfirm]");

      $this->form_validation->set_message('check_currpassword', "The {field} is incorrect.");
      if($this->form_validation->run() == true){
        //make salt Password
        $this->load->model('user_model');
        $salt = $this->user_model->salt();
        $newpassword = $this->user_model->makePassword($this->input->post("newpassword"),$salt);

        //load all data into array
  			$data = array(
  				"password" => $newpassword,
          "salt"     => $salt
  			);
        $this->db->where('user_id', $this->session->userdata('buyer_id'));
        $this->db->update('users', $data);
        echo '<p class="text-success">Password Successfully Updates</p>';
        redirect(base_url(). "BProfilec/settings");
      }
      else{
        echo '<p class="text-danger">Incorrect Password</p>';
      }
    }
    //CHANGE PASSWORD CODE END

    function check_currpassword(){
      $this->load->model('user_model');
      $user_id = $this->session->userdata('user_id');
      $currentpassword = $this->input->post('currentpassword');
      $data = $this->user_model->check_currpassword($user_id, $currentpassword);
      if($data){
        return true;
      }
      else{
        return false;
      }
    }

    //VIEW ALL GIG CODE START
    function view_fellows($school_id){
      if($school_id){
        //$serviceCategory_id = rawurldecod($serviceCategory_id);
        $this->load->model('Aschool_model');
        $school_data['school_data'] = $this->Aschool_model->getUserDataBySchool($school_id);
        $this->load->view('buyer/includes/buyer_VPheader');
        $this->load->view('buyer/includes/buyer_serviceBar');
        $this->load->view('buyer/view_fellows', $school_data);
        $this->load->view('buyer/includes/buyer_footer');
        $this->load->view('buyer/includes/buyer_end');
      }
    }
    //VIEW ALL GIG CODE START

  }

?>
