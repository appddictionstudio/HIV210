<?php
if (! defined ( 'ABSPATH' )) {
	exit ();
}

define ( 'PLUGIN_PATH', trailingslashit ( plugin_dir_path ( __FILE__ ) ) );
define ( 'DEFAULT_MAX_ENTRIES', 200 );

// require_once __DIR__ . '/libs/vendor/autoload.php';
include_once __DIR__ . "/mcharts_utils.php";

if (! class_exists ( 'maxicharts_gravity_forms' )) {
	class maxicharts_gravity_forms {
		// protected static $logger = null;
		function __construct() {
			// MAXICHARTSAPI::getLogger()->debug("construct gfcr_custom_search_criteria");
			MAXICHARTSAPI::getLogger ()->debug ( "Adding Module : " . __CLASS__ );
			
			if ($this->checkGravityForms ()) {
				add_action ( 'maxicharts_add_shortcodes', array (
						$this,
						'add_gravity_forms_shortcode' 
				) );
				
				add_filter ( "maxicharts_get_data_from_source", array (
						$this,
						"get_data_from_gf" 
				), 10, 3 );
				
				add_filter ( 'mcharts_filter_defaults_parameters', array (
						$this,
						'add_default_params' 
				) );
				add_filter ( 'mcharts_return_without_graph', array (
						$this,
						'return_without_graph' 
				) );
			} else {
				MAXICHARTSAPI::getLogger ()->error ( "Missing plugin Gravity Forms" );
			}
		}
		function return_without_graph($atts) {
			MAXICHARTSAPI::getLogger ()->debug ( $atts);
			$type = str_replace ( ' ', '', $atts['type']);
			if ($type === 'array' || $type === 'total' || $type === 'list' || $type === 'sum') {
				return true;
			}
			return false;
		}
		function checkGravityForms() {
			$result = true;
			$gfClassHere = class_exists ( 'GFCommon' );
			// MAXICHARTSAPI::getLogger ()->info ( "GF? ".$gfClassHere." -> ".$gfPluginHere);
			if (! function_exists ( 'is_plugin_active' )) {
				include_once (ABSPATH . 'wp-admin/includes/plugin.php');
			}
			$gfPluginHere = is_plugin_active ( 'gravityforms/gravityforms.php' );
			// MAXICHARTSAPI::getLogger ()->info ( "GF? ".$gfClassHere." -> ".$gfPluginHere);
			
			if (! $gfClassHere && ! $gfPluginHere) {
				// check if gravity forms installed and active
				$msg = "Please install/activate gravityforms plugin";
				MAXICHARTSAPI::getLogger ()->error ( $msg );
				$result = false;
			}
			
			return $result;
		}
		function add_gravity_forms_shortcode() {
			MAXICHARTSAPI::getLogger ()->debug ( "Adding shortcode : gfchartsreports" );
			add_shortcode ( 'gfchartsreports', array (
					$this,
					'gf_charts_shortcode' 
			) );
			
			MAXICHARTSAPI::getLogger ()->debug ( "Adding shortcode : gfentryfieldvalue" );
			add_shortcode ( 'gfentryfieldvalue', array (
					$this,
					'gf_entry_field_value' 
			) );
		}
		function gf_entry_field_value($atts) {
			MAXICHARTSAPI::getLogger ()->debug ( "Executing shortcode : gfentryfieldvalue" );
			if (! is_admin ()) {
				$source = 'gf';
				$destination = 'text';
				return $this->displayFieldValue ( $source, $destination, $atts );
			}
		}
		function gf_charts_shortcode($atts) {
			MAXICHARTSAPI::getLogger ()->debug ( "Executing shortcode : gfchartsreports" );
			if (! is_admin ()) {
				$source = 'gf';
				$destination = 'chartjs';
				return maxicharts_reports::chartReports ( $source, $destination, $atts );
			}
		}
		function add_default_params($defaults) {
			return $defaults;
		}
		function displayFieldValue($source, $destination, $atts) {
			MAXICHARTSAPI::getLogger ()->debug ( "gfentryfieldvalue DO Report from " . $source . " to " . $destination );
			$defaultsParameters = array (
					'lead_id' => '',
					'field_id' => '',
					'style' => '',
					'class' => '' 
			);
			extract ( shortcode_atts ( $defaultsParameters, $atts ) );
			MAXICHARTSAPI::getLogger ()->debug ( $atts );
			$lead_id = str_replace ( ' ', '', $lead_id );
			$field_id = str_replace ( ' ', '', $field_id );
			$style = str_replace ( ' ', '', $style );
			$classParam = str_replace ( ' ', '', $class );
			
			$entry = GFAPI::get_entry ( $lead_id );
			// MAXICHARTSAPI::getLogger ()->debug ( $entry);
			$field_to_display = rgar ( $entry, $field_id );
			MAXICHARTSAPI::getLogger ()->debug ( $field_to_display );
			if ($style) {
				$result = '<span style="' . $style . '">' . $field_to_display . '</span>';
			} else if ($classParam) {
				$result = '<div class="' . $classParam . '">' . $field_to_display . '</div>';
			} else {
				$result = $field_to_display;
			}
			MAXICHARTSAPI::getLogger ()->debug ( $result );
			return $result;
		}
		function listValuesOfFieldInForm($gfEntries, $includes) {
			MAXICHARTSAPI::getLogger ()->debug ( "GF Create list " );
			MAXICHARTSAPI::getLogger ()->debug ( $gfEntries );
			$result = '<ul>';
			
			$answersToCatch = MAXICHARTSAPI::getArrayForFieldInForm ( $gfEntries, $includes );
			
			$result .= implode ( '</li><li>', $answersToCatch );
			$result .= '</ul>';
			return $result;
		}		
		
		
		function buildReportFieldsForGF($form_id, $type, $includeArray, $excludeArray = null, $datasets_invert = null) {
			MAXICHARTSAPI::getLogger ()->debug ( "GF data to dig " . $form_id );
			
			$form = GFAPI::get_form ( $form_id );
			
			// $graphType = $type;
			$allFields = $form ['fields'];
			MAXICHARTSAPI::getLogger ()->debug ( "form fields : " . count ( $allFields ) );
			foreach ( $allFields as $formFieldId => $fieldData ) {
				
				$fieldType = $fieldData ['type'];
				$fieldId = $fieldData ['id'];
				if (! empty ( $includeArray )) {
					if (! in_array ( $fieldId, $includeArray )) {
						continue;
					}
				} else if (! empty ( $excludeArray )) {
					if (in_array ( $fieldId, $excludeArray )) {
						continue;
					}
				}
				// MAXICHARTSAPI::getLogger()->debug($fieldData);
				MAXICHARTSAPI::getLogger ()->debug ( $type . " ### Processing field " . $formFieldId . ' of type ' . $fieldType );
				// $skipField = false;
				// $fieldData = apply_filters('mcharts_filter_gf_field_before_type_process', $formFieldId, $fieldData);
				
				switch ($fieldType) {
					case 'text' :
					case 'textarea' :
					case 'checkbox' :
					case 'radio' :
					case 'survey' :
					case 'select' :
					case 'list' :
						if ($fieldData ['gsurveyLikertEnableMultipleRows'] == 1) {
							MAXICHARTSAPI::getLogger ()->debug ( "MULTI ROW SURVEY LIKERT" );
							MAXICHARTSAPI::getLogger ()->debug ( $fieldData );
							$reportFields [$fieldId] ['choices'] = $fieldData ['choices'];
							$reportFields [$fieldId] ['inputs'] = $fieldData ['inputs'];
							$reportFields [$fieldId] ['gsurveyLikertEnableMultipleRows'] = 1;
							$reportFields [$fieldId] ['multisets'] = 1;
						} else if ($fieldType == 'list' && $fieldData ['enableColumns']) {
							$reportFields [$fieldId] ['choices'] = $fieldData ['choices'];
							$reportFields [$fieldId] ['inputs'] = $fieldData ['inputs'];
							// $reportFields [$fieldId] ['gsurveyLikertEnableMultipleRows'] = 1;
							$reportFields [$fieldId] ['multisets'] = 1;
						} else {
							$reportFields [$fieldId] ['choices'] = $fieldData ['choices'];
						}
						
						// }
						break;
					/*
					 * case 'listtryurtu' :
					 * // Drop Down List Field for Gravity Forms
					 * MAXICHARTSAPI::getLogger ()->debug ('Field type is '.$fieldType. ' create chart on unique entry at the moment...');
					 *
					 * // $list_values = unserialize( rgar( $entry, '3' ) );
					 *
					 * $allDataInList = 1;
					 * if ($allDataInList) {
					 * $xdata = 'Year';
					 * $serie = 'Type of Device';
					 * $data = 'Number';
					 * MAXICHARTSAPI::getLogger ()->debug ($fieldData);
					 * foreach ($fieldData ['choices'] as $subChoices){
					 * if (isset($subChoices['isDropDownChoices'])) {
					 * $reportFields [$fieldId] ['choices'][$subChoices['value']] = $subChoices['$isDropDownChoices'];
					 * }
					 * }
					 * }
					 * // set as all data in list
					 *
					 * break;
					 */
					case 'slider' :
						break;
					case 'number' :
						break;
					
					default :
						MAXICHARTSAPI::getLogger ()->error ( "Unknown field type : " . $fieldType );
						
						continue;
						break;
				}
				
				$reportFields [$fieldId] ['datasets_invert'] = $datasets_invert;
				$reportFields [$fieldId] ['label'] = $fieldData ['label'];
				$reportFields [$fieldId] ['graphType'] = $type;
				$reportFields [$fieldId] ['type'] = $fieldType;
				
				MAXICHARTSAPI::getLogger ()->debug ( "Creating report field  : " . $fieldData ['label'] . ' of type ' . $fieldType . ' -> ' . $type . ' inverted:' . $datasets_invert );
			}
			
			return $reportFields;
		}
		function countAnswers($reportFields, $entries) {
			$countArray = array ();
			if (count ( $entries ) == 0) {
				MAXICHARTSAPI::getLogger ()->error ( 'no entries' );
				return $countArray;
			}
			foreach ( $reportFields as $fieldId => $fieldData ) {
				if (empty ( $fieldId )) {
					MAXICHARTSAPI::getLogger ()->warn ( 'empty field ' . $fieldId );
					continue;
				}
				
				$fieldType = $fieldData ['type'];
				$multiRowsSurvey = isset ( $fieldData ['gsurveyLikertEnableMultipleRows'] ) ? $fieldData ['gsurveyLikertEnableMultipleRows'] == 1 : false;
				// $listCondition
				$multiRowsList = (isset ( $fieldData ['type'] ) && $fieldData ['type'] == 'list') ? $fieldData ['enableColumns'] == 1 : false;
				
				$multiRows = ($multiRowsSurvey || $multiRowsList);
				$multiText = $multiRows ? 'multirows' : 'single row';
				MAXICHARTSAPI::getLogger ()->info ( "--> Counting answers in entries for field " . $fieldType . ' (' . $multiText . ') : ' . $fieldId );
				
				MAXICHARTSAPI::getLogger ()->debug ( print_r ( $fieldData, true ) );
				$countArray [$fieldId] = array ();
				foreach ( $entries as $entry ) {
					MAXICHARTSAPI::getLogger ()->info ( "--> entry " . $entry ['id'] );
					MAXICHARTSAPI::getLogger ()->debug ( $entry );
					foreach ( $entry as $key => $value ) {
						if (empty ( $key ) || empty ( $value )) {
							continue;
						} else {
							MAXICHARTSAPI::getLogger ()->debug ( $key . " => " . $value );
						}
						
						if ($fieldType == 'list') {
							if (trim ( $key ) == trim ( $fieldId )) {
								MAXICHARTSAPI::getLogger ()->debug ("Working onlist field serialized data..." );
								$data = @unserialize ( $value );
								if ($value === 'b:0;' || $data !== false) {
									
									MAXICHARTSAPI::getLogger ()->debug ( $data );
									$countArray [$fieldId] ['answers'] = array_values ( $data );
									// MAXICHARTSAPI::getLogger ()->debug ($data);
								} else {
									MAXICHARTSAPI::getLogger ()->debug ( "not serialized data" );
									
									$countArray [$fieldId] ['answers'] [] = $value;
								}
							}
						} else if ($fieldType == 'checkbox') {
							
							$keyExploded = explode ( '.', $key );
							if (isset ( $keyExploded [0] ) && isset ( $keyExploded [1] ) && $keyExploded [0] == $fieldId) {
								
								$labelForChoice = $reportFields [$fieldId] ['choices'] [$keyExploded [1] - 1] ['text'];
								$labelForChoice = wp_strip_all_tags ( $labelForChoice );
								$countArray [$fieldId] ['answers'] [] = $labelForChoice;
							}
						} else {
							
							if (trim ( $key ) == trim ( $fieldId ) || ($multiRows && strpos ( trim ( $key ), trim ( $fieldId . '.' ) ) !== false)) {
								MAXICHARTSAPI::getLogger ()->debug ( $key . " <- MATCHES -> " . $fieldId );
								if ($fieldType == 'survey') {
									
									if ($multiRows) {
										// need to get label instead of value!
										$newValue = $value;
									} else {
										MAXICHARTSAPI::getLogger ()->debug ( "### SINGLE ROW SURVEY FIELD ###" );
										// need to get label instead of value!
										if (! is_array ( $fieldData )) {
											MAXICHARTSAPI::getLogger ()->warn ( "not an array : " . $fieldData );
											continue;
										}
										
										foreach ( $fieldData as $k => $v ) {
											if (! is_array ( $v )) {
												MAXICHARTSAPI::getLogger ()->warn ( "not an array : " . $v );
												continue;
											}
											foreach ( $v as $keyIdx => $originalChoice ) {
												// MAXICHARTSAPI::getLogger()->debug ( $originalChoice );
												if (trim ( $originalChoice ['value'] ) == trim ( $value )) {
													$newValue = trim ( $originalChoice ['text'] );
													$newValue = wp_strip_all_tags ( $newValue );
												}
											}
										}
									}
								} else {
									$newValue = $value;
								}
								$countArray [$fieldId] ['answers'] [] = apply_filters ( 'mcharts_modify_value_in_answers_array', $newValue );
							}
						}
					}
				}
			}
			return $countArray;
		}
		function getGFEntries($form_id, $maxentries, $custom_search_criteria, $atts) {
			$form = GFAPI::get_form ( $form_id );
			$allEntriesNb = GFAPI::count_entries ( $form_id );
			MAXICHARTSAPI::getLogger ()->debug ( "All entries (also deleted!) : " . $allEntriesNb );
			
			if (empty ( $custom_search_criteria )) {
				$search_criteria ['status'] = 'active';
			} else {
				$search_criteria = json_decode ( $custom_search_criteria, true );
				$search_criteria = apply_filters ( 'mcharts_modify_custom_search_criteria', $search_criteria );
			}
			MAXICHARTSAPI::getLogger ()->debug ( "Search crit : " );
			MAXICHARTSAPI::getLogger ()->debug ( var_export ( $search_criteria, true ) );
			
			$sorting = null;
			$paging = array (
					'offset' => 0,
					'page_size' => $maxentries 
			);
			MAXICHARTSAPI::getLogger ()->debug ( var_export ( $paging, true ) );
			MAXICHARTSAPI::getLogger ()->debug ( $form_id . ' - ' . $search_criteria . ' - ' . $sorting . ' - ' . $paging );
			// $search_criteria = null;
			$entries = GFAPI::get_entries ( $form_id, $search_criteria, $sorting, $paging );
			// $entries = GFAPI::get_entries ( $form_id );
			$nbOfEntries = count ( $entries );
			
			MAXICHARTSAPI::getLogger ()->debug ( "Create complete report for form " . $form_id );
			if ($nbOfEntries) {
				MAXICHARTSAPI::getLogger ()->debug ( "entries : " . $nbOfEntries );
			} else {
				MAXICHARTSAPI::getLogger ()->warn ( "entries : " . $nbOfEntries );
			}
			
			return apply_filters ( 'mcharts_filter_gf_entries', $entries, $atts );
		}
		function computeScores($countArray, $reportFields, $args/*$case_insensitive, $no_score_computation = false*/) {
			if (empty ( $countArray )) {
				$msg = "Error make count of answers";
				MAXICHARTSAPI::getLogger ()->error ( $msg );
				return $msg;
			} else {
				MAXICHARTSAPI::getLogger ()->info ( "At least one item in countarray" );
			}
			
			foreach ( $countArray as $fieldId => $fieldValues ) {
				if (! isset ( $fieldValues ['answers'] )) {
					MAXICHARTSAPI::getLogger ()->warn ( "No answers for field " . $fieldId );
					$reportFields [$fieldId] ['no_answers'] = 1;
					continue;
				}
				$answers = $fieldValues ['answers'];
				MAXICHARTSAPI::getLogger ()->debug ( "--> Computing score for field " . $fieldId );
				// MAXICHARTSAPI::getLogger()->debug ($answers);
				if (boolval ( $args ['no_score_computation'] )) {
					// just take answers
					$reportFields [$fieldId] ['scores'] = array_values ( $answers );
				} else {
					
					if (boolval ( $args ['case_insensitive'] ) == true) {
						$reportFields [$fieldId] ['scores'] = array_count_values ( array_map ( 'strtolower', $answers ) );
					} else {
						$reportFields [$fieldId] ['scores'] = array_count_values ( $answers );
					}
				}
			}
			
			MAXICHARTSAPI::getLogger ()->info ( "Scores size " . count ( $reportFields [$fieldId] ['scores'] ) );
			
			return $reportFields; // apply_filters('mcharts_modify_report_after_scores_count',$reportFields);
		}
		function countDataFor($source, $entries, $reportFields, $args) {
			// $countArray = array();
			$nb_of_entries = count ( $entries );
			MAXICHARTSAPI::getLogger ()->info ( 'Building ' . count ( $reportFields ) . " fields upon " . $nb_of_entries . " entries" );
			if ($nb_of_entries == 0) {
				MAXICHARTSAPI::getLogger ()->error ( 'no entries' );
				return $reportFields;
			} else {
				MAXICHARTSAPI::getLogger ()->info ( 'countDataFor for ' . $nb_of_entries . ' entries' );
			}
			
			$countArray = $this->countAnswers ( $reportFields, $entries );
			MAXICHARTSAPI::getLogger ()->debug ( count ( $countArray ) . ' graph should be displayed' );
			MAXICHARTSAPI::getLogger ()->debug ( $countArray );
			
			$reportFields = $this->computeScores ( $countArray, $reportFields, $args );
			
			$chartTitle = __ ( "Complete report of form " ); // . ' ' . $form_id;
			
			MAXICHARTSAPI::getLogger ()->debug ( $chartTitle );
			$reportFields ['title'] = $chartTitle;
			
			// conversions
			// if ($data_conversion) {
			$reportFields = apply_filters ( 'mcharts_gf_filter_fields_after_count', $reportFields, $args );
			// }
			
			$toDisplay = count ( $reportFields ) - 1;
			MAXICHARTSAPI::getLogger ()->debug ( $toDisplay . ' graph should be displayed' );
			MAXICHARTSAPI::getLogger ()->trace ( $reportFields );
			$reportFields = $this->buildDatasetsAndLabelsFromScores ( $reportFields, $args );
			/*
			 * if ($no_score_computation){
			 * $reportFields = $this->buildDatasetsAndLabelsFromScores($reportFields);
			 * } else {
			 */
			// display answers without computation
			// $reportFields = $this->buildDatasetsAndLabelsFromAnswers($reportFields);
			// }
			
			return $reportFields;
		}
		/*
		 * function buildDatasetsAndLabelsFromAnswers($reportFields) {
		 * MAXICHARTSAPI::getLogger ()->debug ( 'buildDatasetsAndLabelsFromAnswers' );
		 * foreach ( $reportFields as $id => $values ) {
		 * $answers = isset ( $values ['answers'] ) ? $values ['answers'] : '';
		 * if (empty ( $answers)) {
		 * continue;
		 * }
		 *
		 * MAXICHARTSAPI::getLogger()->debug ($answers);
		 * //$percents = isset ( $values ['percents'] ) ? $values ['percents'] : '';
		 * $reportFields [$id] ['labels'] = array_keys ( $answers);
		 * $reportFields [$id] ['data'] = array_values ( $answers);
		 * //$reportFields [$id] ['labels'] = apply_filters ( 'mcharts_modify_singlerow_labels', $reportFields [$id] ['labels'] );
		 *
		 * }
		 *
		 * return $reportFields;
		 * }
		 */
		function buildDatasetsAndLabelsFromScores($reportFields, $args) {
			MAXICHARTSAPI::getLogger ()->debug ( 'buildDatasetsAndLabelsFromScores' );
			foreach ( $reportFields as $id => $values ) {
				$scores = isset ( $values ['scores'] ) ? $values ['scores'] : '';
				if (empty ( $scores )) {
					continue;
				}
				// MAXICHARTSAPI::getLogger()->debug ( "scores:" );
				// MAXICHARTSAPI::getLogger()->debug ( $scores );
				
				$multiRows = isset ( $values ['gsurveyLikertEnableMultipleRows'] ) ? $values ['gsurveyLikertEnableMultipleRows'] == 1 : false;
				if ($multiRows) {
					
					foreach ( $scores as $scoreKey => $scoreValue ) {
						$xyValues = explode ( ':', $scoreKey );
						$datasetName = $xyValues [0];
						$datasetVal = $xyValues [1];
						// MAXICHARTSAPI::getLogger()->debug ( $values['inputs'] );
						foreach ( $values ['inputs'] as $inputIdx => $inputData ) {
							if (trim ( $inputData ['name'] ) == $datasetName) {
								$datasetNameLabel = trim ( $inputData ['label'] );
							}
						}
						foreach ( $values ['choices'] as $inputIdx => $inputData ) {
							if (trim ( $inputData ['value'] ) == $datasetVal) {
								$datasetValLabel = trim ( $inputData ['text'] );
								$datasetValLabel = wp_strip_all_tags ( $datasetValLabel );
							}
						}
						
						if ($values ['datasets_invert']) {
							$reportFields [$id] ['datasets'] [$datasetValLabel] ['data'] [$datasetNameLabel] = $scoreValue;
							$reportFields [$id] ['labels'] [] = $datasetNameLabel;
						} else {
							$reportFields [$id] ['datasets'] [$datasetNameLabel] ['data'] [$datasetValLabel] = $scoreValue;
							$reportFields [$id] ['labels'] [] = $datasetValLabel;
						}
					}
					$reportFields [$id] ['labels'] = apply_filters ( 'mcharts_modify_multirows_labels', $reportFields [$id] ['labels'] );
				} else if ($values ['type'] == 'list') {
					/*
					 * [type] => list
					 * [scores] => Array
					 * (
					 * [0] => Array
					 * (
					 * [Type of Device] => Pills
					 * [Year] => 2014
					 * [Quarter] => Q1
					 * [Number] => 12
					 * )
					 *
					 * [1] => Array
					 * (
					 * [Type of Device] => Pills
					 * [Year] => 2014
					 * [Quarter] => Q2
					 * [Number] => 25
					 * )
					 */
					
					foreach ( $scores as $scoreKey => $scoreValue ) {
						
						$datasetNameLabel = $scoreValue [$args ['list_series_names']];
						$valLabelArray = explode ( '+', $args ['list_series_values'] );
						
						$mappedValLabelArray = array ();
						foreach ( $valLabelArray as $labelPart ) {
							$mappedValLabelArray [] = $scoreValue [$labelPart];
						}
						$datasetValLabel = implode ( ' ', $mappedValLabelArray );
						$scoreDataValue = $scoreValue [$args ['list_labels_names']];
						
						$reportFields [$id] ['datasets'] [$datasetNameLabel] ['data'] [$datasetValLabel] = $scoreDataValue;
						$reportFields [$id] ['labels'] [] = $datasetValLabel;
					}
					
					
					
					$allLabels = $reportFields [$id] ['labels'];
					$allUniqueLabels = array_unique ( $allLabels );
					sort ( $allUniqueLabels );
					$reportFields [$id] ['labels'] = $allUniqueLabels;
					
					
					// add missing keys and sort in order to graph correctly
					foreach ($reportFields [$id] ['datasets'] as $dataSetName => $dataSetData){
						$dataSetLabels =  array_keys($dataSetData['data']);
						$labelDiff = array_diff($allUniqueLabels, $dataSetLabels);
						$newArrayPart = array_fill_keys ( $labelDiff, 0 );
						$newDatasetData = array_merge($reportFields [$id] ['datasets'][$dataSetName]['data'], $newArrayPart);
						ksort($newDatasetData);
						$reportFields [$id] ['datasets'][$dataSetName]['data'] = $newDatasetData;
					}
					
					
					
					
				} else {
					
					$percents = isset ( $values ['percents'] ) ? $values ['percents'] : '';
					$reportFields [$id] ['labels'] = array_keys ( $scores );
					$reportFields [$id] ['data'] = array_values ( $scores );
					$reportFields [$id] ['labels'] = apply_filters ( 'mcharts_modify_singlerow_labels', $reportFields [$id] ['labels'] );
				}
				// MAXICHARTSAPI::getLogger()->debug ( "labels:" );
				// MAXICHARTSAPI::getLogger()->debug ( $reportFields [$id] ['labels'] );
			}
			return $reportFields;
		}
		function createReportFieldForList($reportFields) {
			/*
			 * $data = @unserialize($str);
			 * if ($str === 'b:0;' || $data !== false) {
			 * echo "ok";
			 * } else {
			 * echo "not ok";
			 * }
			 */
			/*
			 * (
			 * [choices] => Array
			 * (
			 * [0] => Array
			 * (
			 * [text] => > 14%
			 * [value] => &gt; 14%
			 * [isSelected] => 1
			 * [price] =>
			 * )
			 *
			 * [1] => Array
			 * (
			 * [text] => 7% <= x <= 14%
			 * [value] => 7% &lt;= x &lt;= 14%
			 * [isSelected] =>
			 * [price] =>
			 * )
			 *
			 * [2] => Array
			 * (
			 * [text] => < 7%
			 * [value] => &lt; 7%
			 * [isSelected] =>
			 * [price] =>
			 * )
			 *
			 * )
			 *
			 * [datasets_invert] =>
			 * [label] => Radio
			 * [graphType] => bar
			 * [type] => radio
			 * [scores] => Array
			 * (
			 * [7% &lt;= x &lt;= 14%] => 3
			 * [&gt; 14%] => 4
			 * [Second Choice] => 9
			 * [Third Choice] => 2
			 * [First Choice] => 12
			 * )
			 *
			 * [labels] => Array
			 * (
			 * [0] => 7%
			 * [1] => > 14%
			 * )
			 *
			 * [data] => Array
			 * (
			 * [0] => 3
			 * [1] => 4
			 * [2] => 9
			 * [3] => 2
			 * [4] => 12
			 * )
			 *
			 * )
			 *
			 */
		}
		function get_data_from_gf($reportFields, $source, $atts) {
			MAXICHARTSAPI::getLogger ()->info ( "Process source " . $source );
			//$reportFields = array ();
			if ($source == 'gf') {
				
				$defaultsParameters = array (
						'type' => 'pie',
						'url' => '',
						'position' => '',
						'float' => false,
						'center' => false,
						'title' => 'chart',
						'canvaswidth' => '625',
						'canvasheight' => '625',
						'width' => '48%',
						'height' => 'auto',
						'margin' => '5px',
						'relativewidth' => '1',
						'align' => '',
						'class' => '',
						'labels' => '',
						'data' => '30,50,100',
						'data_conversion' => '',
						'datasets_invert' => '',
						'datasets' => '',
						'gf_form_ids' => '',
						'multi_include' => '',
						'gf_form_id' => '1',
						'maxentries' => strval ( DEFAULT_MAX_ENTRIES ),
						'gf_criteria' => '',
						'include' => '',
						'exclude' => '',
						'colors' => '',
						'color_set' => '',
						'color_rand' => false,
						'chart_js_options' => '',
						'tooltip_style' => 'BOTH',
						'custom_search_criteria' => '',
						'fillopacity' => '0.7',
						'pointstrokecolor' => '#FFFFFF',
						'animation' => 'true',
						'xaxislabel' => '',
						'yaxislabel' => '',
						'scalefontsize' => '12',
						'scalefontcolor' => '#666',
						'scaleoverride' => 'false',
						'scalesteps' => 'null',
						'scalestepwidth' => 'null',
						'scalestartvalue' => 'null',
						'case_insensitive' => false,
						'no_score_computation' => false,
						'list_series_names' => '',
						'list_series_values' => '',
						'list_labels_names' => '',
						
						// 'gv_approve_status' => 'all',
						// CSV source
						'xcol' => '0',
						'ycol' => '1',
						'compute' => '',
						'header_start' => '0',
						'case_insensitive' => false,
						'header_size' => '1',
						// new CSV
						'columns' => '',
						'rows' => '',
						'delimiter' => '',
						'information_source' => '',
						'no_entries_custom_message' => '',
				);
				extract ( shortcode_atts ($defaultsParameters, $atts ) );

				
				$type = str_replace ( ' ', '', $type );
				$url = str_replace ( ' ', '', $url );
				$title = str_replace ( ' ', '', $title );
				$data = explode ( ',', str_replace ( ' ', '', $data ) );
				$data_conversion = str_replace ( ' ', '', $data_conversion );
				$datasets_invert = str_replace ( ' ', '', $datasets_invert );
				// $gv_approve_status = explode ( ";", str_replace ( ' ', '', $gv_approve_status) );
				$datasets = explode ( "next", str_replace ( ' ', '', $datasets ) );
				$gf_form_ids = explode ( ',', str_replace ( ' ', '', $gf_form_ids ) );
				$multi_include = explode ( ',', str_replace ( ' ', '', $multi_include ) );
				$gf_form_id = str_replace ( ' ', '', $gf_form_id );
				$colors = str_replace ( ' ', '', $colors );
				$color_set = str_replace ( ' ', '', $color_set );
				$color_rand = str_replace ( ' ', '', $color_rand );
				$position = str_replace ( ' ', '', $position );
				$float = str_replace ( ' ', '', $float );
				$center = str_replace ( ' ', '', $center );
				// $information_source = $information_source;
				$case_insensitive = boolval ( str_replace ( ' ', '', $case_insensitive ) );
				$no_score_computation = boolval ( str_replace ( ' ', '', $no_score_computation ) );
				$list_series_names = $list_series_names;
				$list_series_values = $list_series_values;
				$list_labels_names = $list_labels_names;
				
				$include = str_replace ( ' ', '', $include );
				$exclude = str_replace ( ' ', '', $exclude );
				$custom_search_criteria = str_replace ( ' ', '', $custom_search_criteria );
				$tooltip_style = str_replace ( ' ', '', $tooltip_style );
				$xcol = str_replace ( ' ', '', $xcol );
				$columns = maxicharts_reports::get_all_ranges ( $columns );
				$rows = maxicharts_reports::get_all_ranges ( $rows );
				$delimiter = str_replace ( ' ', '', $delimiter );
				MAXICHARTSAPI::getLogger ()->debug ( $columns );
				MAXICHARTSAPI::getLogger ()->debug ( $rows );
				$compute = str_replace ( ' ', '', $compute );
				$maxentries = str_replace ( ' ', '', $maxentries );
				if (empty ( $maxentries )) {
					$maxentries = DEFAULT_MAX_ENTRIES;
				}
				$header_start = str_replace ( ' ', '', $header_start );
				$header_size = str_replace ( ' ', '', $header_size );
				
				
				if ((! empty ( $include ))) {
					$includeArray = explode ( ",", $include );
				}
				if (! empty ( $exclude )) {
					$excludeArray = explode ( ",", $exclude );
				}
				
				MAXICHARTSAPI::getLogger ()->info ( "Get DATAS from GF source " . $source );
				if (! empty ( $gf_form_ids ) && count ( $gf_form_ids ) > 1 && ! empty ( $multi_include ) && count ( $multi_include ) > 1) {
					// process multi-form sources
					MAXICHARTSAPI::getLogger ()->info ( "#### MULTI sources process" );
					
					$multiCombined = array_combine ( $gf_form_ids, $multi_include );
					MAXICHARTSAPI::getLogger ()->info ( $multiCombined );
					$countArray = array ();
					foreach ( $multiCombined as $gf_id => $field_id ) {
						MAXICHARTSAPI::getLogger ()->info ( "#### MULTI " . $gf_id . ' -> ' . $field_id );
						$entries = $this->getGFEntries ( $gf_id, $maxentries, $custom_search_criteria, $atts );
						$currentReportFields = $this->buildReportFieldsForGF ( $gf_id, $type, array (
								$field_id 
						), null, $datasets_invert );

						
						MAXICHARTSAPI::getLogger ()->info ( "#### MULTI Counting " . $gf_id . ' -> ' . $field_id );

						$currentCount = $this->countAnswers ( $currentReportFields, $entries );
						$reportFieldsArray [] = $currentReportFields;

						$answers = reset ( $currentCount ) ['answers'];

						$mergedAnswers = array_merge ( $mergedAnswers, $answers );
						$countArray [] = $currentCount;

					}
					
					MAXICHARTSAPI::getLogger ()->debug ( "#### MULTI DATA RETRIEVED " . count ( $reportFieldsArray ) . ' graph should be merged' );
					MAXICHARTSAPI::getLogger ()->debug ( $countArray );
					MAXICHARTSAPI::getLogger ()->debug ( $reportFieldsArray );
					$reportFields = reset ( $reportFieldsArray );
					MAXICHARTSAPI::getLogger ()->debug ( $reportFields );
					
					MAXICHARTSAPI::getLogger ()->debug ( array_search ( "answers", $countArray ) );
					
					$reportFields = $this->computeScores ( $countArray, $reportFields );
					
					MAXICHARTSAPI::getLogger ()->info ( $rpa );
				} else if (! empty ( $gf_form_id ) && $gf_form_id > 0) {
					MAXICHARTSAPI::getLogger ()->info ( "#### SINGLE source process" );
					$entries = $this->getGFEntries ( $gf_form_id, $maxentries, $custom_search_criteria, $atts );
					if (count ( $entries ) > 0) {
						if ($type === 'total') {
							return count ( $entries );
						} if ($type === 'sum') {
							//$totalCount = 0;
							MAXICHARTSAPI::getLogger ()->info ( "SUM #### ".count ( $entries )." entries of field(s) ".implode('/',$includeArray) );
							$sumArray = array();
							foreach ($includeArray as $field_id_to_count) {
								foreach ($entries as $entry){
									$sumArray[] = intval(rgar($entry, strval($field_id_to_count)));
								}
							}
							return array_sum($sumArray);
						} else if ($type === 'array') {
							MAXICHARTSAPI::getLogger ()->info ( "#### array type" );
							$result = MAXICHARTSAPI::getArrayForFieldInForm ( $entries, $includeArray );
							MAXICHARTSAPI::getLogger ()->info ( "#### array type result" );
							MAXICHARTSAPI::getLogger ()->info ( $result );
							return $result;
						} else if ($type === 'list') {
							return $this->listValuesOfFieldInForm ( $entries, $includeArray );
						}
						$reportFields = $this->buildReportFieldsForGF ( $gf_form_id, $type, isset ( $includeArray ) ? $includeArray : null, isset ( $excludeArray ) ? $excludeArray : null, $datasets_invert );
						MAXICHARTSAPI::getLogger ()->debug ( count ( $reportFields ) . ' graph(s) should be displayed' );
						
						if (empty ( $reportFields )) {
							$msg = "No data available for fields";
							MAXICHARTSAPI::getLogger ()->warn ( $msg );
							return $msg;
						} else if ($type == 'list') {
							return $this->listValuesOfFieldInForm($reportFields);
						} else {
							MAXICHARTSAPI::getLogger ()->debug ( '### Entries values computation on report fields:', count ( $reportFields ) );
							MAXICHARTSAPI::getLogger ()->debug ( $reportFields );
							// json_encode($record, JSON_PRETTY_PRINT) . PHP_EOL
							/*
							 * if ($unique_entry_stats){
							 * // show graph for unique entry (list with a lot of data for ex.)
							 *
							 * $reportFields = $this->createReportFieldForList($reportFields);
							 * } else {
							 */
							/*
							 * $args = array(
							 * 'data_conversion' => $data_conversion,
							 * 'case_insensitive'=> $case_insensitive,
							 * 'no_score_computation'=>$no_score_computation,
							 * 'list_parameters' => array(
							 * 'list_series_names' => $list_series_names,
							 * 'list_series_values' => $list_series_values,
							 * 'list_labels_names' => $list_labels_names),
							 * );
							 */
							$reportFields = $this->countDataFor ( $source, $entries, $reportFields, $atts );
							// }
						}
					} else {

						$form_object = GFAPI::get_form( $gf_form_id);
						$formTile = '';
						if (!is_wp_error($form_object)){
							$formTile = $form_object['title'];
						}
						
						if (empty($no_entries_custom_message)){
							$displayed_msg = "No answer to form ". '<em> '.$formTile.' </em>'."yet";
						} else {
							$displayed_msg = $no_entries_custom_message;
						}
						
						MAXICHARTSAPI::getLogger ()->warn ( $displayed_msg);
						return $displayed_msg;

					}
				}
			}
			
			MAXICHARTSAPI::getLogger ()->info (__CLASS__.' returns '.count($reportFields).' report fields');
			
			return $reportFields;
		}
	}
}

new maxicharts_gravity_forms ();