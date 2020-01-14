<?php
/**
 * 输出 Excel文件的类
 */
//先导入第三方类库
require_once('PHPExcel.php');

/**
 * 再次封装一次,以便使用
 * @author "蓝冰大侠" "vYao"
 */
class SExcel{
	
	//当前实例
	protected static $_instance;
	
	//当前工作表对象索引 默认为0
	private static $_sheetIndex = 0;
	private static $_sheetName = '';
	
	//当前工作表默认起始行数 默认为1
	private static $_line = 1;
	
	/**
	 * 获取PHPExcel实例
	 * @return PHPExcel
	 */
	public static function getInstance(){
		if(is_null(self::$_instance)){
			self::$_instance = new PHPExcel();
		}
		
		return self::$_instance;
	}
	
	/**
	 * 构造方法
	 */
	private function __construct(){
	}
	
	/**
	 * 生成新的sheet
	 * @param $index int 工作表索引
	 * @return void
	 * @throws \Exception
	 */
	public static function createSheet($index){
		$phpExcel = self::getInstance();
		$phpExcel->createSheet($index);
		self::$_sheetIndex = $index;
		self::$_line = 1;
	}
	
	/**
	 * 指定sheet名称
	 * @param $name string 名称
	 * @return void
	 * @throws \Exception
	 */
	public static function setSheetName($name){
		self::$_sheetName = $name;
	}

	/**
	 * 获取当前sheet实例
	 * @return \PHPExcel_Worksheet
	 * @throws \Exception
	 */
	public static function sheetInstance(){
		
		$sheet = self::getInstance()->setActiveSheetIndex(self::$_sheetIndex);
		
		if(self::$_sheetName){
			$sheet->setTitle(self::$_sheetName);
		}
		
		return $sheet;
	}
	
	/**
	 * 设置一行数据 (未封装的样式自行封装)
	 * @param $title array 行数据   ['data1', 'data2', ['data3', 'col2']]  col为占用两列
	 * @param $range array 列范围
	 * @param $style array 样式
	 * [
	 *  'backGroundColor' => ['default' =>'6CA6CD', '2' => 'EEEFFF'],
	 *  'align' => ['default' => ['horizontal' => 'center', 'vertical' => 'center']],
	 *  'border' => ['default' => ['top' => '', 'bottom' => '', 'left' => , 'right' => '']]
	 * ]
	 * @throws \Exception
	 */
	public static function setDataToRow($title, $range = ['A'], $style = []){

		$sheet = self::sheetInstance();
		
		$range = self::getRangeByWord($range);

		//记录列索引
		$index = 0;
		foreach($title as $k => $v){
		
			//如果超出列范围 则跳出
			if(!isset($range[$index])){
				break;
			}
	
			//当前列和行的位置 例如A1
			$position = $range[$index] . self::$_line;
		
			if(!is_array($v)){
				//设置某一列某一行数据
				$sheet->setCellValue($position, $v);
				
				$index++;
			}else{
				//设置某一列某一行数据
				$sheet->setCellValue($position, $v[0]);
				
				$index = ($index + $v[1]);
			
				//末位列 如果超出范围 则取最后一位
				$endPosition = ($range[($index - 1)] ?? end($range)) . self::$_line;
				
				if($endPosition != $position){
					
					$position = $position . ':' . $endPosition;
			
					//指指定列合并
					$sheet->mergeCells($position);
				}
			}
			
			//样式设置
			if($style){
				//获取样式对象
				$sheetStyle = $sheet->getStyle($position);
				
				//根据样式参数，设置对应样式
				foreach($style as $sk => $sv){
					self::setStyle($sheetStyle, $sk, $sv, $k);
				}
			}
		}
		
		self::$_line ++;
	}
	
	/**
	 * 设置列宽 可根据业务扩展此方法
	 * @param $range array
	 * @param $width string 宽度
	 * @return void
	 * @throws \Exception
	 */
	public static function setRangeWidth($range, $width){
		
		$sheet = self::getInstance()->setActiveSheetIndex(self::$_sheetIndex);
		
		$row = self::getRangeByWord($range);
		
		//每行设置15的宽度  注：这里可以对每一列的元素进行长度分析 选择最大值来设定宽度
		foreach($row as $v){
			$sheet->getColumnDimension($v)->setWidth($width);
		}
	}
	
	/**
	 * 最终导出
	 * @param $fileName string 文件名称
	 * @param $format string 导出类型
	 * @return void
	 * @throws \Exception
	 */
	public static function exploreRun($fileName, $format = 'Excel2007'){
	
		$phpExcel = self::getInstance();
		
		//默认指向第一页
		$phpExcel->setActiveSheetIndex(0);

		$fileName = iconv('utf-8', 'gbk', $fileName);
		
		if($format == 'Excel5'){
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
			$objWriter->save('php://output');
		}else{
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
			$objWriter->save('php://output');
		}
	}
	
	/**
	 * 设置样式
	 * @param $sheetStyle PHPExcel_Style 样式对象
	 * @param $styleKey string 样式key
	 * @param $styleValue array 指定的样式值
	 * @param $titleKey int title中的索引值
	 * @return boolean
	 * @throws \Exception
	 */
	private static function setStyle($sheetStyle, $styleKey, $styleValue, $titleKey){
		//获取参数指定值
		$value = self::getStyleValue($styleValue, $titleKey);
		
		if(!$value){
			return false;
		}
		
		switch($styleKey){
			case 'backGroundColor':
				//设置填充颜色
				$objFill = $sheetStyle->getFill();
				$objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$objFill->getStartColor()->setARGB('FF' . $value);
				break;
			case 'align':
				//设置对其方式
				$objAlign = $sheetStyle->getAlignment();
				$objAlign->setHorizontal($value['horizontal'] ?? PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objAlign->setVertical($value['vertical'] ?? PHPExcel_Style_Alignment::VERTICAL_CENTER);
				break;
			case 'border':
				//设置边框 none;dashDot;dashDotDot;dashed;dotted;double;hair;medium;mediumDashDot;mediumDashDotDot;mediumDashed;slantDashDot;thick;thin
				$objBorder = $sheetStyle->getBorders();
				if(isset($value['top'])){
					$objBorder->getTop()->setBorderStyle($value['top'] ?: PHPExcel_Style_Border::BORDER_NONE);
				}
				if(isset($value['bottom'])){
					$objBorder->getBottom()->setBorderStyle($value['bottom'] ?: PHPExcel_Style_Border::BORDER_NONE);
				}
				if(isset($value['left'])){
					$objBorder->getLeft()->setBorderStyle($value['left'] ?: PHPExcel_Style_Border::BORDER_NONE);
				}
				if(isset($value['right'])){
					$objBorder->getRight()->setBorderStyle($value['right'] ?: PHPExcel_Style_Border::BORDER_NONE);
				}
				break;
			default :
				break;
		}
		return true;
	}
	
	/**
	 * 根据字母获取列的区间
	 * @param $titleKey int title中的索引值
	 * @param $styleValue array style配置参数
	 * @return string | array
	 * @throws \Exception
	 */
	private static function getStyleValue($styleValue, $titleKey){
		//先获取style中是否存在指定title的样式
		if(isset($styleValue[$titleKey])){
			return $styleValue[$titleKey];
		}
		
		//获取默认style
		if(isset($styleValue['default'])){
			return $styleValue['default'];
		}
		
		return '';
	}
	
	public static $wordAll = [
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
		'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
		//'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ'
	];
	
	/**
	 * 根据字母获取列的区间
	 * @param $word array 字母区间  例如 ['A', 'G']
	 * @return array
	 * @throws \Exception
	 */
	public static function getRangeByWord($word){
		if(count($word) == 1){
			return $word;
		}
		
		if(count($word) > 2){
			trigger_error('$word参数格式应该为: A-E', E_USER_ERROR);
		}
		
		$start = array_search($word[0], self::$wordAll);
		$end = array_search($word[1], self::$wordAll);
		
		return array_slice(self::$wordAll, $start, $end - $start + 1);
	}
}