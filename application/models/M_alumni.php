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

class M_alumni extends CI_Model {

	/**
	 * Primary key
	 * @var string
	 */
	public static $pk = 'id';

	/**
	 * Table
	 * @var string
	 */
	public static $table = 'students';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get data for pagination
	 * @param string
	 * @param int
	 * @param int
	 * @return Query
	 */
	public function get_where($keyword, $limit = 0, $offset = 0, $sort_field = '', $sort_type = 'ASC') {
		$this->db->select('id, nis, full_name, gender, street_address, photo, start_date, end_date, is_deleted');
		$this->db->where('is_alumni', 'true');
		$this->db->group_start();
		$this->db->like('nis', $keyword);
		$this->db->or_like('full_name', $keyword);
		$this->db->or_like('gender', $keyword);
		$this->db->or_like('street_address', $keyword);
		$this->db->or_like('start_date', $keyword);
		$this->db->or_like('end_date', $keyword);
		$this->db->group_end();
		if ($sort_field != '') {
			$this->db->order_by($sort_field, $sort_type);
		}
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}
		return $this->db->get(self::$table);
	}

	/**
	 * Get All Data
	 * @return Query
	 */
	public function get_all() {
		return $this->db
			->select('id, nis, full_name, gender, street_address, photo, start_date, end_date')
			->where('is_alumni', 'true')
			->get(self::$table);
	}

	/**
	 * Get Total row for pagination
	 * @param string
	 * @return int
	 */
	public function total_rows($keyword) {
		return $this->db
			->where('is_alumni', 'true')
			->group_start()
			->like('nis', $keyword)
			->or_like('full_name', $keyword)
			->or_like('gender', $keyword)
			->or_like('street_address', $keyword)
			->or_like('start_date', $keyword)
			->or_like('end_date', $keyword)
			->group_end()
			->count_all_results(self::$table);
	}
}