<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CMS Sekolahku | CMS (Content Management System) dan PPDB/PMB Online GRATIS 
 * untuk sekolah SD/Sederajat, SMP/Sederajat, SMA/Sederajat, dan Perguruan Tinggi
 * @version    2.0.0
 * @author     Anton Sofyan | https://facebook.com/antonsofyan | 4ntonsofyan@gmail.com | 0857 5988 8922
 * @copyright  (c) 2014-2017
 * @link       http://sekolahku.web.id
 * @since      Version 2.0.0
 *
 * PERINGATAN :
 * 1. TIDAK DIPERKENANKAN MEMPERJUALBELIKAN APLIKASI INI TANPA SEIZIN DARI PIHAK PENGEMBANG APLIKASI.
 * 2. TIDAK DIPERKENANKAN MENGHAPUS KODE SUMBER APLIKASI.
 * 3. TIDAK MENYERTAKAN LINK KOMERSIL (JASA LAYANAN HOSTING DAN DOMAIN) YANG MENGUNTUNGKAN SEPIHAK.
 */

class Admin_Controller extends MY_Controller {

	/**
	 * Primary key
	 * @var string
	 */
	protected $pk;

	/**
	 * Table
	 * @var string
	 */
	protected $table;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		// Restrict
		$this->auth->restrict();
		
		// Check privileges Users
		if (!in_array($this->uri->segment(1), $this->session->userdata('user_privileges'))) {
			redirect(base_url());
		}

		// $this->output->enable_profiler();
	}

	/**
	 * deleted data | SET is_deleted to true
	 */
	public function delete() {
		$response = [];
		$ids = explode(',', $this->input->post($this->pk));
		if (count($ids) > 0) {
			if($this->model->delete($ids, $this->table)) {
				$response = [
		        	'action' => 'delete',
					'type' => 'success',
					'message' => 'deleted',
					'id' => $ids
				];
			} else {
				$response = [
					'action' => 'delete',
					'type' => 'error',
					'message' => 'not_deleted'
				];
			}
		} else {
			$response = [
				'action' => 'delete',
				'type' => 'warning',
				'message' => 'not_selected'
			];
			
		}

		$this->output
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($response, JSON_PRETTY_PRINT))
			->_display();
		exit;
	}

	/**
	 * Restored data | SET is_deleted to false
	 */
	public function restore() {
		$response = [];
		$ids = explode(',', $this->input->post($this->pk));
		if (count($ids) > 0) {
			if($this->model->restore($ids, $this->table)) {
				$response = [
		        	'action' => 'restore',
					'type' => 'success',
					'message' => 'restored',					
					'id' => $ids
				];
			} else {
				$response = [
					'action' => 'restore',
					'type' => 'error',
					'message' => 'not_deleted'
				];
			}
		} else {
			$response = [
				'action' => 'restore',
				'type' => 'warning',
				'message' => 'not_restored'
			];
		}

		$this->output
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($response, JSON_PRETTY_PRINT))
			->_display();
		exit;
	}

	/**
	 * Email Check
	 * @return Boolean
	 */
	public function email_check($str, $id) {
		$exist = false;
		if ($this->model->is_email_exist('email', $str, 'students', $id)) {
			$exist = true;
		}
		if ($this->model->is_email_exist('email', $str, 'employees', $id)) {
			$exist = true;
		}
		if ($this->model->is_email_exist('user_email', $str, 'users', $id)) {
			$exist = true;
		}
		if ($exist) {
			$this->form_validation->set_message('email_check', 'Email sudah digunakan. Silahkan gunakan email lain');
			return false;
		}
		return true;
	}
}