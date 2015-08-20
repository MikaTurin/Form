<?php  namespace Msz\Forms;


class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSubmited()
    {

        $_GET['tst1_myfrm_sbm'] = 1;
        $this->assertTrue(
            Form::make('tst1')->setMethodGet()->isSubmited(),
            'get submit failed'
        );

        $_POST['test'] = 'test';
        $this->assertTrue(
            Form::make('tst2')->setMethodPost()->isSubmited(),
            'post submit failed'
        );

    }
}
