<?php
class MultiSelectField extends FormField {
  protected $sourceObject, $keyField, $labelField;
  function __construct($name, $title, $sourceObject, $keyField = "ID", $labelField = "Title") {
    $this->sourceObject = $sourceObject;
    $this->keyField = $keyField;
    $this->labelField = $labelField;
    $this->itemsArray = array ();
    parent::__construct($name, $title);
  }
  public function FieldHolder() {
    Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
    Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery_improvements.js");
    Requirements::javascript("multiselectfield/javascript/multiselectfield.js");
    Requirements::css('multiselectfield/css/multiselectfield.css');
    return $this->renderWith("MultiSelectField");
  }
  function getSelected() {
    $items = array ();
    $keyField = $this->keyField;
    $labelField = $this->labelField;
    if ($this->form) {
      $fieldName = $this->name;
      $record = $this->form->getRecord();
      if (is_object($record) && $record->hasMethod($fieldName)) {
        foreach ($record->$fieldName() as $item) {
          $items[$item->$keyField] = $item->$labelField;
        }
      }
    }
    return $items;
  }
  function getUnselected() {
    $items = array ();
    $keyField = $this->keyField;
    $labelField = $this->labelField;
    $selectedItems = $this->getSelected();
    $sourceRecords = DataObject::get($this->sourceObject);
    foreach ($sourceRecords as $item) {
      if (! isset ($selectedItems[$item->$keyField])) {
        $items[$item->$keyField] = $item->$labelField;
      }
    }
    return $items;
  }
  public function UnselectedField() {
    $unselectedItems = $this->getUnselected();
    $output = "<select multiple class=\"source\">\n";
    foreach ($unselectedItems as $itemId=>$itemTitle) {
      $output .= "<option value=\"$itemId\">$itemTitle</option>";
    }
    $output .= "</select>\n";
    return $output;
  }
  public function SelectedField() {
    $selectedItems = $this->getSelected();
    $output = "<select multiple class=\"destination\" >\n";
    $hidden = "";
    foreach ($selectedItems as $itemId=>$itemTitle) {
      $output .= "<option value=\"$itemId\">$itemTitle</option>";
      $hidden .= "$itemId,";
    }
    $output .= "</select>\n";
    $hidden = trim($hidden, ',');
    $output .= "<input type=\"hidden\" value=\"$hidden\" id=\"$this->id\" name=\"$this->name\">\n";
    return $output;
  }
  function saveInto(DataObject $record) {
    $items = array ();
    $fieldName = $this->name;
    $saveDest = $record->$fieldName();
    if (!$saveDest) {
      user_error("MultiSelectField::saveInto() Field '$fieldName' not found on $record->class.$record->ID", E_USER_ERROR);
    }
    if ($this->value) {
      $items = split(",", $this->value);
      $saveDest->setByIDList($items);
    }
  }
  function performReadonlyTransformation() {
    $values = '';
    if ($selected = $this->getSelected()) {
      foreach($selected as $item) {
        $values .= "$item, ";
      }
      $values = trim($values,", ");
    }
    $title = ($this->title)?$this->title:'';
    $field = new ReadonlyField($this->name, $title, $values);
    $field->setForm($this->form);
    return $field;
  }

}
?>
