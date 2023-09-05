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

class Users extends Admin_Controller {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model(['m_users', 'm_user_groups']);
		$this->pk = M_users::$pk;
		$this->table = M_users::$table;
	}

	/**
	 * Index
	 */
	public function index() {
		$this->vars['title'] = 'SEMUA PENGGUNA';
		$this->vars['acl'] = $this->vars['users'] = true;
		$this->vars['user_groups_dropdown'] = json_encode($this->m_user_groups->dropdown());
		$this->vars['content'] = 'users/read';
		$this->load->view('backend/index', $this->vars);
	}

	/**
	 * Pagination
	 */
	public function pagination() {
		$page_number = (int) $this->input->post('page_number', true);
		$limit = (int) $this->input->post('per_page', true);
		$keyword = trim($this->input->post('keyword', true));
		$sort_field = trim($this->input->post('sort_field', true));
		$sort_type = trim($this->input->post('sort_type', true));
		$offset = ($page_number * $limit);
		$query = $this->m_users->get_where($keyword, $limit, $offset, $sort_field, $sort_type);
		$sql = $this->db->last_query();
		$total_rows = $this->m_users->total_rows($keyword);
		$total_page = $limit > 0 ? ceil($total_rows / $limit) : 1;
		$response = [];
		if ($query->num_rows() > 0) {
			$rows = [];
			foreach($query->result() as $row) {
				$rows[] = $row;
			}
			$response = [
				'total_page' => $total_page,
				'total_rows' => $total_rows,
				'rows' 		 => $rows
			];
		} else {
			$response = [
				'total_page' => 0,
				'total_rows' => 0
			];
		}

		$response['sql'] = $sql;
		$this->output
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($response, JSON_PRETTY_PRINT))
			->_display();
		exit;
	}

	/**
	 * find_id
	 * @param 	int $id
	 * @return 	Object 
	 */
	public function find_id() {
		$id = $this->input->post('id', true);
		$query = [];
		if ($id && $id != 0 && ctype_digit((string) $id)) {
			$query = $this->model->RowObject($this->table, $this->pk, $id);
		}
		
		$this->output
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($query, JSON_PRETTY_PRINT))
			->_display();
		exit;
	}

	/**
	 * Save or Update
	 * @return 	Object 
	 */
	public function save() {
		$id = $this->input->post('id', true);
		$response = [];
		if ($this->validation($id)) {
			$fill_data = $this->fill_data();
			if ($id && $id != 0 && ctype_digit((string) $id)) {
				$fill_data['updated_by'] = $this->session->userdata('id');
				$response['action'] = 'update';		
				$response['type'] = $this->model->update($id, $this->table, $fill_data) ? 'success' : 'error';
				$response['message'] = $response['type'] == 'success' ? 'updated' : 'not_updated'; 
			} else {
				$fill_data['created_at'] = NULL;
				$fill_data['created_by'] = $this->session->userdata('id');
				$response['action'] = 'save';
				$response['type'] = $this->model->insert($this->table, $fill_data) ? 'success' : 'error';
				$response['message'] = $response['type'] == 'success' ? 'created' : 'not_created';
			}
		} else {
			$response['action'] = 'validation_errors';
			$response['type'] = 'error';
			$response['message'] = validation_errors();
		}

		$this->output
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($response, JSON_PRETTY_PRINT))
			->_display();
		exit;
	}

	/**
	 * fill_data
	 * @param int 
	 * @return array
	 */
	private function fill_data() {
		$data = [];
		$data['user_name'] = $this->input->post('user_name', true);
		$user_password = $this->input->post('user_password', true);
		if ($user_password) {
			$data['user_password'] = password_hash($user_password, PASSWORD_BCRYPT);
		}
		$data['user_email'] = $this->input->post('user_email') ? $this->input->post('user_email', true) : NULL;
		$data['user_url'] = $this->input->post('user_url') ? prep_url($this->input->post('user_url', true)) : NULL;
		$data['user_full_name'] = $this->input->post('user_full_name', true);
		$data['biography'] = $this->input->post('biography', true);
		$data['user_group_id'] = $this->input->post('user_group_id') ? $this->input->post('user_group_id') : 0;
		return $data;
	}

	/**
	 * Validations Form
	 * @param int 
	 * @return Boolean
	 */
	private function validation($id = 0) {
		$this->load->library('form_validation');
		$val = $this->form_validation;
		$val->set_rules('user_name', 'User Name', 'trim|required');
		if ($id && $id != 0 && ctype_digit((string) $id)) {
			$val->set_rules('user_password', 'Password', 'trim|min_length[6]');
		} else {
			$val->set_rules('user_password', 'Password', 'trim|required|min_length[6]');
		}
		$val->set_rules('user_email', 'Email', 'trim|required|valid_email');
		$val->set_rules('user_url', 'URL', 'trim|valid_url');
		$val->set_rules('user_full_name', 'Full Name', 'trim');
		$val->set_rules('biography', 'Biography', 'trim');
		$val->set_error_delimiters('<div>&sdot; ', '</div>');
		return $val->run();
	}
}