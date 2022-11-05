<?php
	require __DIR__ . '/vendor/autoload.php';

	use \Curl\Curl; //import curl lib
	
	define("PCO_API_KEY", "");
	define("PCO_API_SECRET", "");
	define("PCO_SERVICE_TYPE", "");
	
	class PCOToXML
	{
		public function __construct($serviceType = PCO_SERVICE_TYPE) {
			$planList = $this->listPlans($serviceType);
			$planId = $planList->data[0]->id;
			$plan = $this->listItems($serviceType, $planId);
			$file = $this->planToXML($plan);
			$this->download($file);
		}
		public function download($content, $fileName = 'import.xml'){
			header("Content-type: application/xml");
			header("Content-Disposition: attachment; filename=\"$fileName\"");
			echo $content;
		}
		public function planToXML($plan){
			$file = file_get_contents('template.xml');
			$replace = '';
			$numberCount = 1;
			foreach($plan->data as $item){
				if($item->attributes->item_type != 'header'){
					$comment = "";
					if(isset($_GET["comments"]) && !filter_var($_GET["comments"], FILTER_VALIDATE_BOOLEAN)){
						// no comments please
					}else{
						$itemNotes = $this->curl($item->links->self . "/item_notes"); // fetch the item notes
						// check if any notes are from Audio / Visual
						foreach($itemNotes->data as $note){
							if($note->attributes->category_name == "Audio/Visual"){
								// inject notes into XML
								$comment = $note->attributes->content;
							}
						}	
					}
					// remove new lines (from comment)
					$replace .= ($numberCount > 1 ? '			' : '') . '<Cue comment="' . str_replace(array("\r", "\n"), '', $comment) . '" number="' . $numberCount . '" name="' . htmlspecialchars($item->attributes->title) . '" trigger="halt" milliseconds="1000"/>' . "\n";
					
					$numberCount++;
				}
			}
			return str_replace('{cue_list}', $replace, $file);
		}
		
		public function listPlans($serviceId){
			return $this->curl("https://api.planningcenteronline.com/services/v2/service_types/$serviceId/plans?filter=future");
		}
		public function listItems($serviceId, $planId) {
			return $this->curl("https://api.planningcenteronline.com/services/v2/service_types/$serviceId/plans/$planId/items");
		}
		public function listItemNotes($serviceId, $planId, $itemId){
			return $this->curl("https://api.planningcenteronline.com/services/v2/service_types/$serviceId/plans/$planId/items/$itemId/item_notes");
		}
		public function curl($url){
			$curl = new Curl();
			$curl->setBasicAuthentication(PCO_API_KEY, PCO_API_SECRET);
			
			if ($curl->error) {
				return false;
			}
			return $curl->get($url);
		}
	}
	new PCOToXML();
?>
