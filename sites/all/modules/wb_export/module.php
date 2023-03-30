<?php
	function wb_export_menu() {
		$items = array();
		$items['export.xml'] = array(
			'title' => 'EXPORT',
			'page callback' => 'wb_export_generate',
			'access callback' => 'user_access',
			'access arguments' => array('access content'),
			'type' => MENU_NORMAL_ITEM,
		 );
		return $items;
	}
	
	function wb_export_generate() {
		header('Content-type: text/xml; charset=utf-8');
		$siteName = variable_get('site_name', $_SERVER['SERVER_NAME']);
		
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<catalog>
				<updated>' . date('Y-m-d H:i:s') . '</updated>
				<shop>
					<name>' . htmlspecialchars($siteName) . '</name>
					<description><![CDATA[' . htmlspecialchars(t('Добро пожаловать в хобби-маркет Vyazhem.kz! Мы рады приветствовать Вас на страницах нашего сайта Vyazhem.kz интернет-магазин пряжи и товаров для рукоделия в Павлодаре. Огромный ассортимент пряжи: Alize, YarnArt, Gazzal, Himalaya, Vita, Seam, Nako. Аксессуары для вязания от лучших производителей: ChiaGoo, Addi, KnitPro, Gamma, Рукоделие, Pony, Maxvell. Осуществляем доставка по всему Казахстану до двери.')) . ']]></description>
				</shop>
				<categories>' . wb_export_categories() . '</categories>
				<products>' . wb_export_products() . '</products>
			</catalog>';
		
		//echo();
		die($xml);
	}
	
	function wb_export_categories($idParent = 0) {
		$xml = null;
		
		$sql = "
			SELECT 
			  taxonomy_term_data.tid as id,
			  taxonomy_term_hierarchy.parent as idParent,
			  taxonomy_term_data.name as category
			from taxonomy_term_data inner JOIN 
				 taxonomy_vocabulary on taxonomy_vocabulary.vid = taxonomy_term_data.vid inner JOIN 
				 taxonomy_term_hierarchy on taxonomy_term_hierarchy.tid = taxonomy_term_data.tid
			WHERE taxonomy_vocabulary.machine_name = 'category' and 
				  taxonomy_term_hierarchy.parent = :idParent
			ORDER by taxonomy_term_data.tid desc";
		$dbRows = db_query($sql, [':idParent' => $idParent])->fetchAll();
		
		if(is_array($dbRows) and !empty($dbRows)) {
			foreach($dbRows as $row) {
				$xml .= '
					<category>
						<id>' . ((int) $row->id) . '</id>
						<parent>' . ((int) $row->idParent) . '</parent>
						<name><![CDATA[' . htmlspecialchars($row->category) . ']]></name>
					</category>' . wb_export_categories($row->id);
			}
			
		}
		
		return $xml;
	}
	function wb_export_products() {
		$xml = null;
		
		$sql = "
			SELECT 
			  node.nid as id,
			  field_data_field_category.field_category_tid as category,
			  node.title as name,
			  field_data_body.body_value as description,
			  field_data_field_price.field_price_value as price,
			  field_data_field_composition.field_composition_value AS specification,
			  file_managed.uri as image
			FROM node inner JOIN 
				 field_data_field_category on field_data_field_category.entity_id = node.nid and 
											  field_data_field_category.bundle = node.type LEFT JOIN 
				 field_data_body ON field_data_body.entity_id = node.nid and 
									field_data_body.bundle = node.type inner join 
				 field_data_field_price ON field_data_field_price.entity_id = node.nid and 
										   field_data_field_price.bundle = node.type LEFT JOIN 
				 field_data_field_composition ON field_data_field_composition.entity_id = node.nid and 
												 field_data_field_composition.bundle = node.type LEFT JOIN 
				 field_data_field_image on field_data_field_image.entity_id = node.nid and 
										   field_data_field_image.bundle = node.type LEFT JOIN 
				 file_managed on file_managed.fid = field_data_field_image.field_image_fid
			where node.type = 'product' and 
				  node.status = 1";
		$dbRows = db_query($sql)->fetchAll();
		
		if(is_array($dbRows) and !empty($dbRows)) {
			$host = 'https://';
			if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
				$host = 'http://';
			}
			foreach($dbRows as $row) {
				
				$image = null;
				if(!is_null($row->image) and trim($row->image) <> '') {
					$image = file_create_url($row->image);
				}
				if(!is_null($image)) { $image = '<![CDATA[' . $image . ']]>'; }
				
				$description = null;
				if(!is_null($row->description) and trim($row->description) <> '') {
					$description = strip_tags($row->description);
					$description = htmlspecialchars($description);
				}
				
				$specification = null;
				if(!is_null($row->specification) and trim($row->specification) <> '') {
					$specification = strip_tags($row->specification);
					$specification = htmlspecialchars($specification);
				}
				if(!is_null($specification) and trim($specification) <> '') {
					$specification = '
						<specifications>
							<specification>
								<title><![CDATA[' . t('Общие характеристики') . ']]></title>
								<rows>
									<row>
										<label>' . t('Состав') . '</label>
										<value><![CDATA[' . $specification . ']]></value>
									</row>
								</rows>
							</specification>
						</specifications>';
				}
				$xml .= '
					<product>
						<id>' . $row->id . '</id>
						<category>' . $row->category . '</category>
						<name><![CDATA[' . $row->name . ']]></name>
						<url><![CDATA[' . $host . $_SERVER['SERVER_NAME'] . '/node/' . $row->id . ']]></url>
						<image>' . $image . '</image>
						<description><![CDATA[' . $description . ']]></description>
						<price>' . $row->price . '</price>
						' . $specification . '
					</product>';
			}
		}
		
		return $xml;
	}
?>