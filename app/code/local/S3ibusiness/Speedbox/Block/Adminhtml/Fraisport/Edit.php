<?php
	
class S3ibusiness_Speedbox_Block_Adminhtml_Fraisport_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "speedbox";
				$this->_controller = "adminhtml_fraisport";
				$this->_updateButton("save", "label", Mage::helper("speedbox")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("speedbox")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("speedbox")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("fraisport_data") && Mage::registry("fraisport_data")->getId() ){

				    return Mage::helper("speedbox")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("fraisport_data")->getId()));

				} 
				else{

				     return Mage::helper("speedbox")->__("Add Item");

				}
		}
}