<?php
/**
 * Created by lsv
 * Date: 9/4/13
 * Time: 6:29 AM
 */

namespace Datapump\Tests\Magmi;

use Datapump\Product\Data\Category;
use Datapump\Product\Data\RequiredData;
use Datapump\Product\Imageproduct;
use Datapump\Product\ItemHolder;
use Datapump\Product\Simple;
use Datapump\Product\Configurable;
use Datapump\Product\Stock;
use Datapump\Tests\Booter;

/**
 * @requires extension mysqli
 */
class MagmiTest extends Booter
{

    /**
     * @var RequiredData
     */
    protected $configRequiredData;

    /**
     * @var RequiredData
     */
    protected $simpleRequiredData;

    /**
     * @var \Datapump\Product\ItemHolder
     */
    private $itemholder;

    private $itemsInserted = 0;

    public function __construct()
    {

        parent::__construct();
        parent::initializeDatabase();

        $this->itemholder = new ItemHolder(self::getLogger());

        $this->simpleRequiredData = new RequiredData();
        $this->simpleRequiredData
            ->setShortDescription('short description')
            ->setDescription('long description')
            ->setTax(0)
            ->setWeight(100);

        $this->configRequiredData = new RequiredData();
        $this->configRequiredData
            ->setShortDescription('short description')
            ->setDescription('long description');

        $config = new Configurable(
            clone $this->configRequiredData
                ->setSku('config1')
                ->setName('config1')
        , 'color');

        $category = new Category();
        $category->set('active-config');
        $category->set('inactive-config', false);
        $category->set('level01/level02/level03');
        $config->injectData($category);

        $config->addSimpleProduct(new Simple(
            clone $this->simpleRequiredData
                ->setSku('configsimple-sku1')
                ->setName('configsimple-sku1')
                ->setQty(12)
                ->setPrice(120)
                ->set('special_price', 100)
                ->set('color', 'blue')
        ));

        $config->addSimpleProduct(new Simple(
            clone $this->simpleRequiredData
                ->setSku('configsimple-sku2')
                ->setName('configsimple-sku2')
                ->setQty(24)
                ->setPrice(110)
                ->set('special_price', 80)
                ->set('color', 'green')
        ));

        $this->itemholder->addProduct($config);

        $this->itemholder->addProduct(new Simple(
            clone $this->simpleRequiredData->setSku('simple-sku1-dontmanagestock')
                ->setQty(null)
                ->setPrice(500)
                ->setName('simple-sku1-dontmanagestock')
        ));
        $this->itemholder->addProduct(new Simple(
            clone $this->simpleRequiredData->setSku('simple-sku2')
                ->setQty(10)
                ->setPrice(400)
                ->setName('simple-sku2')
        ));
        $this->itemholder->addProduct(new Simple(
            clone $this->simpleRequiredData->setSku('simple-sku3')
                ->setQty(-10)
                ->setPrice(300)
                ->setName('simple-sku3')
        ));
        $this->itemholder->addProduct(new Simple(
            clone $this->simpleRequiredData->setSku('simple-sku4')
                ->setQty(0)
                ->setPrice(200)
                ->setName('simple-sku4')
        ));

        $req = new RequiredData();
        $req
            ->setType(RequiredData::TYPE_SIMPLE)
            ->setSku('foobar');
        $this->itemholder->addProduct(new Imageproduct($req));


        $product1 = new Stock(new RequiredData());
        $product1->set('sku', 'stock-sku1')->set('qty', 10);

        $product2 = new Stock(new RequiredData());
        $product2->set('sku', 'stock-sku2')->set('qty', 30);

        $this->itemholder->addProduct($product1)->addProduct($product2);

        $this->itemsInserted = $this->itemholder->countProducts();

    }

    public function test_canTestForMagmi()
    {
        $this->setExpectedException('Datapump\Exception\MagmiHasNotBeenSetup');
        $holder = new ItemHolder(self::getLogger());
        $holder->import();
    }

    public function test_ProductsNotInserted()
    {
        $q = parent::getDatabase()->prepare('SELECT COUNT(*) AS num FROM catalog_product_entity');
        $q->execute();
        $num = $q->fetch();
        $this->assertEquals(0, $num['num']);
    }

    public function test_CanInjectData()
    {
        $this->itemholder->setMagmi(
            \Magmi_DataPumpFactory::getDataPumpInstance("productimport"),
            'travis',
            ItemHolder::MAGMI_CREATE_UPDATE
        )->import();
    }

    public function test_ProductsInserted()
    {
        $q = parent::getDatabase()->prepare('SELECT COUNT(*) AS num FROM catalog_product_entity');
        $q->execute();
        $num = $q->fetch();
        $this->assertEquals($this->itemsInserted, $num['num']);
    }

    public function test_checkProductsStock()
    {

    }

    public function test_checkProductsPrice()
    {

    }

    public function test_checkProductsSpecialPrice()
    {

    }

}