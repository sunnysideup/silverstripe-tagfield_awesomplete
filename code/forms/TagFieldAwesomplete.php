<?php


class TagFieldAwesomplete extends TagField
{

    /**
     * {@inheritdoc}
     */
    public function Field($properties = array())
    {
        $this->addExtraClass('ss-tag-field');

        if ($this->getIsMultiple()) {
            $this->setAttribute('multiple', 'multiple');
        }

        if ($this->shouldLazyLoad) {
            $this->setAttribute('data-ss-tag-field-suggest-url', $this->getSuggestURL());
        } else {
            $properties = array_merge($properties, array(
                'Options' => $this->getOptions()
            ));
        }

        $this->setAttribute('data-can-create', (int) $this->getCanCreate());

        return $this
            ->customise($properties)
            ->renderWith(array("templates/TagFieldAwesomeplete"));
    }

    /**
     * Returns array of arrays representing tags.
     *
     * @param string $term
     *
     * @return array
     */
    protected function getTags($term)
    {
        /**
         * @var DataList $source
         */
        $source = $this->getSource();

        $titleField = $this->getTitleField();

        $query = $source
            ->filter($titleField . ':PartialMatch:nocase', $term)
            ->sort($titleField)
            ->limit($this->getLazyLoadItemLimit());

        // Map into a distinct list
        $items = array();
        $titleField = $this->getTitleField();
        foreach ($query->map('ID', $titleField) as $id => $title) {
            $items[$title] = array(
                'id' => $id,
                'text' => $title
            );
        }

        return array_values($items);
    }

}

class TagFieldAwesomplete_Readonly extends TagField_Readonly
{
    protected $readonly = true;

    /**
     * Render the readonly field as HTML.
     *
     * @param array $properties
     * @return HTMLText
     */
    public function Field($properties = array())
    {
        $options = array();

        foreach ($this->getOptions()->filter('Selected', true) as $option) {
            $options[] = $option->Title;
        }

        $field = ReadonlyField::create($this->name.'_Readonly', $this->title);

        $field->setForm($this->form);
        $field->setValue(implode(', ', $options));
        return $field->Field();
    }
}
