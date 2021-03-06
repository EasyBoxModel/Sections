<?php

namespace EBM\UIApplication;

use EBM\Section\AbstractBaseSection;
use EBM\ModelAdapters\UserAdapter as User;
use EBM\Exception\UIApplicationException;

abstract class AbstractUIApplication
{
    public $sections = [];
    public $sectionInstances = [];
    public $sectionSlugs = [];

    static $_instances = [];

    public function __construct()
    {
        $this->addSections($this->sections);
    }

    public function addSections(Array $sections = [])
    {
        foreach ($sections as $section) {
            $sectionInstance = new $section($this);

            $slug = $sectionInstance->getSlug();

            $this->addSectionInstance($slug, $sectionInstance)
                ->addSectionSlug($slug);
        }

        return $this;
    }

    public function addSectionInstance(String $slug, AbstractBaseSection $section)
    {
        $this->sectionInstances[$slug] = $section;

        return $this;
    }

    public function addSectionSlug(String $slug)
    {
        $this->sectionSlugs[] = $slug;

        return $this;
    }

    public function getSections(): array
    {
        return $this->sectionInstances;
    }

    public function getSectionBySlug(String $slug = null)
    {
        if (!$slug) {
            return null;
        }

        $sections = $this->getSections();

        foreach ($sections as $section) {
            if ($section->getSlug() == $slug) {
                return $section;
            }
        }

        return null;
    }

    public function getSectionSlugs(): array
    {
        return $this->sectionSlugs;
    }

    public function getNextSectionSlug(String $currentSlug)
    {
        $slugs = $this->getSectionSlugs();

        $slug = null;

        for ($i = 0; $i < count($slugs); $i++) {
            if ($slugs[$i] == $currentSlug) {
                $slug = isset($slugs[$i+1]) ? $slugs[$i+1] : null;
            }
        }

        return $slug;
    }

    /**
     * Returns the first section slug
     *
     * @return String slug
     */
    public function getFirstSectionSlug(): String
    {
        return $this->getSectionSlugs()[0];
    }

    /**
     * registerInstances
     *
     * Initializes the $_instances array with null service names that will become instantiated singletons on $this->getService($name)
     *
     * @return ServiceProvider
     */
    final public function registerInstance(String $name, $instance)
    {
        if (!$this->instanceIsSet($name)) {
            self::$_instances[$name] = $instance;
        }

        return $this;
    }

    /**
     * instanceIsSet
     *
     * Returns a ServiceProvider child instance
     *
     * @param String $name; the alias of the service
     *
     * @return ServiceProvider
     */
    final public function instanceIsSet(String $name): bool
    {
        return isset(self::$_instances[$name]);
    }

    /**
     * getInstance
     *
     * Returns a ServiceProvider child instance
     *
     * @param String $name; the alias of the service
     *
     * @return ServiceProvider
     */
    final public function getInstance(String $name)
    {
        if (!$this->instanceIsSet($name)) {
            throw new UIApplicationException("The instance [$name] is not registered", 0);
        }

        return self::$_instances[$name];
    }

    /**
     * getInstances
     *
     * Returns the ServiceProvider registered instances
     *
     * @return ServiceProvider
     */
    final public function getInstances()
    {
        return self::$_instances;
    }

    /**
     * Loops through each section and checks whether it is complete or not
     *
     * @return Bool
     */
    public function isComplete(): bool
    {
        $sections = $this->getSections();

        if (empty($sections)) {
            return true;
        }

        foreach ($sections as $section) {
            $section->setFields();

            if (!$section->isComplete()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Loops through each section and returns the incomplete section
     *
     * @return AbstractBaseSection
     */
    public function getPendingSection()
    {
        $sections = $this->getSections();

        if (empty($sections)) {
            return null;
        }

        foreach ($sections as $section) {
            $section->setFields();

            if (!$section->isComplete()) {
                return $section;
            }
        }

        return null;
    }

    /**
     * Inserts a section instance after an existing section instance
     */
    public function addSectionAfter(String $afterSection, String $newSection)
    {
        $slugs = $this->getSectionSlugs();

        $sections = $this->getSections();

        $afterSectionSlug = (new $afterSection($this))->getSlug();

        $afterSectionPosition = array_search($afterSectionSlug, array_keys($sections));

        $newSectionInstance = new $newSection($this);

        $newSectionSlug = $newSectionInstance->getSlug();

        $new[$newSectionSlug] = $newSectionInstance;

        $newPosition = $afterSectionPosition + 1;

        // Insert the new section after the pointer
        array_splice($sections, $newPosition, 0, $new);

        // Update the section slugs as well
        array_splice($slugs, $newPosition, 0, $newSectionSlug);

        // Set the name of the new pointer to the new section slug
        $keys = array_keys($sections);
        $keys[$newPosition] = $newSectionSlug;

        $this->sectionInstances = array_combine($keys, $sections);

        return $this;
    }
}
