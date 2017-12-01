<?php

/**
 * XML-persisted collection.
 *
 * ------------------------------------------------------------------------
 */
class XML_Model extends Memory_Model
{
//---------------------------------------------------------------------------
//  Housekeeping methods
//---------------------------------------------------------------------------

    /**
     * Constructor.
     * @param string $origin Filename of the XML file
     * @param string $keyfield  Name of the primary key field
     * @param string $entity    Entity name meaningful to the persistence
     */
    function __construct($origin = null, $keyfield = 'id', $entity = null)
    {
        parent::__construct();

        // guess at persistent name if not specified
        if ($origin == null)
            $this->_origin = get_class($this);
        else
            $this->_origin = $origin;

        // remember the other constructor fields
        $this->_keyfield = $keyfield;
        $this->_entity = $entity;
        if($entity !== null)
            $this->load->model($entity);
        // start with an empty collection
        $this->_data = array(); // an array of objects
        $this->_fields = array(); // an array of strings
        // and populate the collection
        $this->load();
    }

    /**
     * Load the collection state appropriately, depending on persistence choice.
     * OVER-RIDE THIS METHOD in persistence choice implementations
     */
    protected function load()
    {
        //---------------------
        if (($xml = simplexml_load_file($this->_origin)) !== FALSE)
        {
            $record = $this->buildEntity();
            $this->loadChildElements($xml, $record);
        }
        // --------------------
        // rebuild the keys table
        $this->reindex();
    }

    /**
     * A function to recursively go through all the child elements in an objects
     * created from XML.  If there are no child elements, it returns FALSE,
     * otherwise it returns TRUE.
     */
    private function loadChildElements($parent, &$item) {
        $hasChildren = FALSE;
        foreach($parent as $key => $value) {
            $hasChildren = TRUE;
            $record = $this->buildEntity();
            if($this->loadChildElements($value, $record) === FALSE) {
                if(!in_array($key, $this->_fields))
                    array_push($this->_fields, $key);
                $item->$key = (string) $value;
            } else {
                $key = $record->{$this->_keyfield};
                $this->_data[$key] = $record;
            }
        }
        return $hasChildren;
    }

    protected function store() {
        $xml = new SimpleXMLElement('<' . static::class. '></' . static::class . '>');
        foreach($this->_data as $key => $record) {
            $xmlRecord = $xml->addChild($this->_entity);
            foreach($record as $id => $value) {
                $xmlRecord->addChild($id, htmlspecialchars($value));
            }
        }
        file_put_contents($this->_origin, $xml->asXML());
    }

    public function buildEntity() {
        if($this->_entity === null)
            return new stdClass();
        $entity = $this->_entity;
        return new $entity();
    }
}
