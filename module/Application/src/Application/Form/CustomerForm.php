<?php
namespace Application\Form;

use Doctrine\ORM\EntityManager;
use SharengoCore\Service\CountriesService;
use Zend\Form\Form;

/**
 * Class DishesForm
 * @package Administrator\Form
 */
class CustomerForm extends Form
{
    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct(CountriesService $countriesService, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct('customer');

        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        $this->add([
            'name'       => 'gender',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'    => 'gender',
                'class' => 'form-control',
            ],
            'options'    => [
                'value_options' => [
                    'male'   => 'Sig.',
                    'female' => 'Sig.ra'
                ]
            ]
        ]);

        $this->add([
            'name'       => 'name',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'name',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'surname',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'surname',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'email',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'email',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        ]);

        /*
        $this->add([
            'name'       => 'birthDate',
            'type'       => 'Zend\Form\Element\Date',
            'attributes' => [
                'id'       => 'birthDate',
                'class'    => 'form-control datepicker-date',
                'max'      => date_create()->format('Y-m-d'),
                'required' => 'required',
                'type'     => 'text'
            ]
        ]);*/

        $this->add([
            'name'       => 'birthCountry',
            'type'       => 'Zend\Form\Element\Select',
            'attributes' => [
                'id'       => 'birthCountry',
                'class'    => 'form-control',
                'required' => 'required'
            ],
            'options'    => [
                'value_options' => $countriesService->getAllCountries()
            ]
        ]);

        $this->add([
            'name'       => 'birthProvince',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'       => 'birthProvince',
                'class'    => 'form-control',
                'required' => 'required'

            ],
        ]);

        $this->add([
            'name'       => 'birthTown',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'birthTown',
                'maxlength' => 32,
                'class'     => 'form-control',
                'required'  => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'address',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'address',
                'maxlength' => 64,
                'class'     => 'form-control',
                'required'  => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'addressInfo',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'addressInfo',
                'maxlength' => 64,
                'class'     => 'form-control',
                'required'  => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'zipCode',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'zipCode',
                'maxlength' => 12,
                'class'     => 'form-control',
                'required'  => 'required'
            ]
        ]);

        $this->add([
            'name'       => 'town',
            'type'       => 'Zend\Form\Element\Text',
            'attributes' => [
                'id'        => 'town',
                'maxlength' => 16,
                'class'     => 'form-control',
                'required'  => 'required'
            ]
        ]);
    }

    public function saveData()
    {
        $customer = $this->getData();
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return $customer;
    }
}