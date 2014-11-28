<?php


namespace Ups\Entity;

use DOMDocument;
use DOMElement;
use Ups\NodeInterface;
class CustomerClassification implements NodeInterface{
        /** @deprecated */
    public $Code;
    /** @deprecated */
    public $Description;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $description;
    public function toNode(DOMDocument $document = null) {
         if (null === $document) {
            $document = new DOMDocument();
        }

        $node = $document->createElement('CustomerClassification');
        $node->appendChild($document->createElement('Code', $this->getCode()));
        $node->appendChild($document->createElement('Description', $this->getDescription()));
        return $node;
    }


    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
}
