<?php


/**
 * Support for generating html code
 *
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: html.php 82 2012-08-16 15:07:10Z quannv $
 **/
defined('_JEXEC') or die('Restricted access');

class AHtml
{

	static $countries;
	/**
	 * Get time picker gui selector.
	 *
	 * @param string $value
	 * @param string $field
	 * @return string HTML
	 */
	function getTimePicker($value, $field, $withTzOffset = true, $params = '')
	{
		static $id;
		if (is_null($id)) {
			$id = 1;
		} else {
			$id ++;
		}
		$picker = 'timePicker' . $id;
		$toggler = 'timePickerToggler' . $id;
		$holder = 'timePickerHolder' . $id;
		if ($withTzOffset) {
			$time = AHtml::date($value, ATIME_FORMAT_SHORT);
		} else {
			$time = AHtml::date($value, ATIME_FORMAT_SHORT, 0);
		}
		if ($withTzOffset) {
			$hour = (int) AHtml::date($value, 'H');
		} else {
			$hour = (int) AHtml::date($value, 'H', 0);
		}
		if ($withTzOffset) {
			$minute = (int) AHtml::date($value,  'i');
		} else {
			$minute = (int) AHtml::date($value,  'i', 0);
		}
		$code = '<input type="text" name="' . $field . '" value="' . $time . '" id="' . $picker . '" size="5" ' . $params . '/>';
		$code .= '<img src="' . IMAGES . 'icon-16-clock.png" id="' . $toggler . '" alt="' . JText::_('Open time picker') . '" class="clock"/>';
		$code .= '<div id="' . $holder . '" class="time_picker_div"></div>';
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration("
				window.addEvent('domready',
				function() {
				timePickers.push(
				new TimePicker('$holder', '$picker', '$toggler',
				{
				format24: true,
				imagesPath:\"" . TIME_PICKER_IMAGES . "\",
				startTime: {
				hour: $hour,
				minute: $minute
	}
	}
		)
		)
	}
		)
				");
		return $code;
	}

	/**
	 * Get dropdown list by added data
	 *
	 * @param string $field name
	 * @param string $noSelectText default value label
	 * @param array $items dropdown items
	 * @param int $selected current item
	 * @param boolean $autoSubmit autosubmit form on change dropdown list true/false
	 * @param string $customParams custom dropdown params like style or class params
	 * @param string name of param of items which may be used like value param in select box
	 * @param
	 * @return string HTML code
	 */
	static function  getFilterSelect($field, $noSelectText, $items, $selected, $autoSubmit = false, $customParams = '', $valueLabel = 'value', $textLabel = 'text')
	{
		$first = new stdClass();
		$first->$valueLabel = 0;
		$first->$textLabel = '- ' . JText::_($noSelectText) . ' -';
		array_unshift($items, $first);
		$customParams = array(trim($customParams));
		if ($autoSubmit) {
			$customParams[] = 'onchange="this.form.submit()"';
		}
		$customParams = implode(' ', $customParams);
		
		return JHTML::_('select.genericlist', $items, $field, $customParams, $valueLabel, $textLabel, $selected);
	}

	/**
	 * Render multiple list filter by added name, options and select values
	 *
	 * @param string $name filter name, use for name and id param
	 * @param string $options usable options
	 * @param string $select select filter values from request
	 * @return string HTML code
	 */
	function renderMultipleFilter($name, $options, $select)
	{
		$code = '<select name="' . $name . '[]" id="' . $name . '" size="3" multiple="multiple" onchange="this.form.submit()" class="inputbox">';
		foreach ($options as $value => $label) {
			$code .= '<option value="' . htmlspecialchars($value) . '"' . (in_array($value, $select) ? ' selected="selected" ' : '') . '>' . JText::_($label) . '</option>';
		}
		$code .= '</select>';
		return $code;
	}
	/**
	 * Generates a HTML check box or boxes
	 * @param array An array of objects
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected. Can be array of keys or just one key
	 * @param string The name of the object variable for the option value
	 * @param string The name of the object variable for the option text
	 * @returns string HTML for the select list
	 */
	function checkBoxList( &$arr, $tag_name, $tag_attribs, $selected=null, $key='value', $text='text' ) {
		reset( $arr );
		$html = "";
		for ($i=0, $n=count( $arr ); $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = @$arr[$i]->id;

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					$k2 = $obj;
					if ($k == $k2) {
						$extra .= " checked=\"checked\" ";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " checked " : '');
			}
			$html .= "\n\t<input type=\"checkbox\" name=\"$tag_name\" value=\"".$k."\"$extra $tag_attribs />" . $t;
		}
		$html .= "\n";
		return $html;
	}
	/**
	 * Get checkbox HTML
	 *
	 * @param int $value if 1 checkbox is checked
	 * @param string $field name, use for name and id param
	 * @return string HTML
	 */
	function getCheckbox($value, $field, $extraValue = null, $autoSubmit = false)
	{
		$code = '<input type="checkbox" class="inputCheckbox" name="' . $field . '" id="' . $field . '" value="' . (is_null($extraValue) ? 1 : $extraValue) . '" ' . ($value !== false ? 'checked="checked"' : '');
		$code .= ($autoSubmit ? ' onclick="document.adminForm.submit()" ' : '') . '/>' . PHP_EOL;
		return $code;
	}

	function getFilterCheckbox($field, $value, $extraValue, $image, $templateImage = false, $text = null, $color = null)
	{
		$code = '<span class="cfilter" title="' . htmlspecialchars($text, ENT_QUOTES, ENCODING) . '">' . PHP_EOL;
		$code .= AHtml::getCheckbox($value, $field, $extraValue, true);
		if ($image) {
			$code .= '<img src="' . IMAGES . 'icon-16-' . $image . '.png" alt="" onclick="$(\'' . $field . '\').checked=!$(\'' . $field . '\').checked;document.adminForm.submit();" style="cursor: pointer;" />';
		} else {
			$code .= '<label for="' . $field . '" class="text" style="color: ' . $color . '">' . JText::_($text) . '</label>';
		}
		$code .= '</span>' . PHP_EOL;
		return $code;
	}

	/**
	 * Set page title by JToolBarHelper object like "OBJECT_TITLE:[task]",
	 * where task take from request and OBJECT_TITLE and icon is given by function parameter.
	 *
	 * @param string $title object title
	 * @param string $icon image name
	 */
	function title($title, $icon, $ctitle = 'Bookpro')
	{
		JToolBarHelper::title($ctitle . ': ' . JText::_($title) /*. ' <small><small>[ ' . ucfirst(JText::_(JRequest::getString('task'))) . ' ]</small></small>'*/, $icon);
	}

	function getReadmore($text, $length = null)
	{
		$text = strip_tags($text);
		$text = JString::trim($text);
		if ($length) {
			$text = JString::substr($text, 0, $length + 1);
			$last = JString::strrpos($text, ' ');
			if ($last) {
				$text = JString::substr($text, 0, $last);
				$run = true;
				while ($run) {
					$slength = JString::strlen($text);
					if ($slength == 0) {
						break;
					}
					$last = JString::substr($text, $slength - 1, 1);
					switch ($last) {
						case '.':
						case ',':
						case '_':
						case '-':
							$text = JString::substr($text, 0, $slength - 1);
							break;
						default:
							$run = false;
							break;
					}
				}
				$text .= ' ...';
			}
		}
		return $text;
	}

	/**
	 * Make custom HTML tooltip.
	 *
	 * @param string $header Header text displayed with icon
	 * @param string $text Text displayed after open tooltip or on mouse icon over
	 * @return string HTML code
	 */
	function info($header, $text)
	{
		$header = JString::trim(JText::_($header));
		$text = JString::trim(JText::_($text));

		if ($header && $text)
			$title = htmlspecialchars($header, ENT_QUOTES) . '::' . htmlspecialchars($text, ENT_QUOTES);
		else
			$title = htmlspecialchars($header . $text);

		$html = '<div class="topInfo editlinktip hasTip" title="' . $title . '" onclick="ACommon.info(this)">' . PHP_EOL;
		$html .= '  <span>' . $header . '</span>' . PHP_EOL;
		$html .= '  <p style="display: none">' . $text . '</p>' . PHP_EOL;
		$html .= '  <div class="clr"></div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;

		return $html;
	}
	
/**
	 * Add button
	 * @param string $name: Label button
	 * @param string $icon: Icon
	 * @param string $class: style
	 * @param string $task: task
	 * @param boolean $list: Ask for select option
	 * @param string $type: submit or button
	 * @param string $id: id of button
	 * 
	 */
	public static function addButton($name,$icon,$class,$task,$list,$type="button",$id=false){
		$html='';
		$html .='<button ';
		$html .='class="'.$class.'" type="'.$type.'"';
		if ($id){
			$html .=' id="'.$id.'"';
		}
		$html .=' onclick="'.self::getCommand($task,$list).'"';
		$html .='><i class="icon-'.$icon.'"></i>&nbsp'.JText::_($name).'</button>';
		return $html;		
	}
	
	private static function getCommand($task,$list){
		$message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');		
		
		if ($list)
		{
			$cmd = "if (document.adminForm.boxchecked.value==0){alert('$message');}else{ Joomla.submitbutton('$task')}";
		}
		else
		{
			$cmd = "Joomla.submitbutton('$task')";
		}
		
		return $cmd;
	}
	
	static public function getCountryList(){
		if(isset(self::$countries)){
			return self::$countries;
		}
		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/models/countries.php';
    	$model = new BookProModelCountries();
		self::$countries = $model->getCompaxList();
		return self::$countries;
		
	}
	
	private static function getCountriesSelectList($select,$name,$attr,$id = null){
		$options 	= self::getCountryList();
		return JHTML::_('select.genericlist', $options, $name, ' class="inputbox" ', 'code', 'name', $select,$id) ;
	}
    
	private static function getCountriesJquerySelectList(){
		$options = self::getCountryList();
		$result = '';
		foreach($options as $item){
			$result .='<option  value="'.$item->code.'">'.($item->name).'</option>';			
		}
		$options =null;
		return $result;	
	}
	
	public static function getLanguageSelectList($data,$name,$attr = array()){
		//debug($data);die;
		$result = '<div>';
		$data = json_decode($data);
		$id = 0;
		if($data){
			foreach ($data as $key=>$val){
				if(is_object($val)){
					$val='';
					}
				$result .= '<div class="clearfix">';
				$result .= self::getCountriesSelectList($key, $name.'[code][]','class="input-mini"');
				$result .= '<input type="text" name="'.$name.'[val][]" value="'.$val.'" />';
				$result .= '<a href="javascript:void(0);" title="'.JText::_("JTOOLBAR_REMOVE").'"><span class="icon-unpublish" onclick="jQuery(this).parent().parent().remove();"></span></a>
							</div>';
				$id++;
			}
		}
		$result .= '<div class="clearfix">';
		$result .= self::getCountriesSelectList('', $name.'[code][]','class="input-mini"');
		$result .= '<input type="text" name="'.$name.'[val][]">';
		$result .= '</div>';
		//add button
		$result	.='<button type="button" class="btn btn-success btn-tiny" onclick="var clone = jQuery(this).parent().children().eq(0).clone();clone.insertBefore(jQuery(this));"><icon class="icon-new"></icon>'.JText::_('COM_BOOKPRO_ADD_LANGUAGE').'</button>';
		$result .= '</div>';
		return $result;
		
		
	}
	public static function getLanguageTextArea($data,$name,$attr = array()){
		$result = '<div>';
		$data = json_decode($data);
		$id = 0;
		if($data){
			foreach ($data as $key=>$val){
				$result .= '<div class="clearfix">';
				$result .= self::getCountriesSelectList($key, $name.'[code][]','class="input-mini"');
				$result .= '<textarea type="text" name="'.$name.'[val][]">'.$val.'</textarea>';
				$result .= '<a href="javascript:void(0);" title="'.JText::_("JTOOLBAR_REMOVE").'"><span class="icon-unpublish" onclick="jQuery(this).parent().parent().remove();"></span></a>
							</div>';
				$id++;
			}
		}
		$result .= '<div class="clearfix">';
		$result .= self::getCountriesSelectList('', $name.'[code][]','class="input-mini"');
		$result .= '<textarea type="text" name="'.$name.'[val][]"></textarea>';
		$result .= '</div>';
		//add button
		$result	.='<button type="button" class="btn btn-success btn-tiny" onclick="var clone = jQuery(this).parent().children().eq(0).clone();clone.insertBefore(jQuery(this));"><icon class="icon-new"></icon>'.JText::_('COM_BOOKPRO_ADD_LANGUAGE').'</button>';
		$result .= '</div>';
		return $result;
		
		
	}
	
	
			

}

?>