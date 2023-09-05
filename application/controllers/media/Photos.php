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

class Photos extends Admin_Controller {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('m_photos');
		$this->pk = M_photos::$pk;
		$this->table = M_photos::$table;
	}

	/**
	 * Index
	 */
	public function index() {
		$this->vars['title'] = 'PHOTO';
		$this->vars['media'] = $this->vars['albums'] = true;
		$this->vars['content'] = 'albums/photos';
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
		$query = $this->m_photos->get_where($keyword, $limit, $offset, $sort_field, $sort_type);
		$total_rows = $this->m_photos->total_rows($keyword);
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

		$this->output
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($response, JSON_PRETTY_PRINT))
			->_display();
		exit;
	}

	/**
	 * Deleted Permanently
	 */
	public function delete() {
		$response = [];
		$ids = explode(',', $this->input->post($this->pk));
		$success = 0;
		foreach($ids as $id) {
			$query = $this->model->RowObject($this->table, $this->pk, $id);
			if($this->m_photos->delete_permanently($id)) {
				@chmod(FCPATH.'media/albums/large/'.$query->photo_name, 0777);
				@chmod(FCPATH.'media/albums/medium/'.$query->photo_name, 0777);
				@chmod(FCPATH.'media/albums/thumbnail/'.$query->photo_name, 0777);
				@unlink(FCPATH.'media/albums/large/'.$query->photo_name);
				@unlink(FCPATH.'media/albums/medium/'.$query->photo_name);
				@unlink(FCPATH.'media/albums/thumbnail/'.$query->photo_name);
				$success++;
			}
		}
		$response = [
        	'action' => 'delete_permanently',
			'type' => 'info',
			'message' => $success.' record deleted successfully.'
		];

		$this->output
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($response, JSON_PRETTY_PRINT))
			->_display();
		exit;
	}
}