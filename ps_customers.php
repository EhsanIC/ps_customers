<?php 


use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;

if(!defined('_PS_VERSION_')) {
    exit;
}

class ps_customers extends Module {

    



    public function __construct()
    {
        $this->name = 'ps_customers';
        $this->tab  = 'other';
        $this->author = 'erfan hossienzadeh';
        $this->version = '1.0.0';

        $this->need_instance = 1;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0' ,
            'max' => '1.7.9'
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = 'manage customers';
        $this->description = 'manage your customer easy';

        $this->confirmUninstall = 'Are You Sure You Want UnInstall ?';
        
    } 

    public function install()
    {
        return parent::install()
            // for adding new column to customers grid * برای اضافه کردن ستون جدید به شبکه مشتریان
            && $this->registerHook('actionCustomerGridDefinitionModifier')
            //  for modifying customers grid sql * برای اصلاح شبکه مشتریان sql
            && $this->registerHook('actionCustomerGridQueryBuilderModifier')
            //  for adding new field to customers create or edit form field * برای افزودن فیلد جدید به مشتریان، فیلد فرم را ایجاد یا ویرایش کنید.
            // && $this->registerHook('actionCustomerFromBuilderModifier')
            // to execute the saving process of added field from the module * برای اجرای فرآیند ذخیره سازی فیلد اضافه شده از ماژول
            // && $this->registerHook('actionAfterCreateCustomerFormHandler')
            // to execute the update process of added field from the module * برای اجرای فرآیند به روز رسانی فیلد اضافه شده از ماژول
            // && $this->registerHook('actionAfterUpdateCustomerFromHandler')
            && $this->installTables();
    }

    public function hookActionCustomerGridDefinitionModifier($params)
    {
        $definition = $params['definition'];

        $translator = $this->getTranslator();

        $definition
            ->getColumns()
            ->addAfter(
                'optin',
                (new ToggleColumn('is_allow_for_review'))
                    ->setName($translator->trans('Allow For Review'))
                    ->setOptions([
                        'field' => 'is_allowed_for_review',
                        'primary_field' => 'id_customer',
                        'route' => 'ps_customers_toggle_is_allowed_for_review',
                        'route_param_name' => 'id_customer'               
                    ])
            );

        $definition->getFilter()->add(
            (new Filter('is_allowed_for_review' , YesAndNoChoiceType::class))
            ->setAssociatedColumn('is_allowed_for_review')
        );
    }

    public function hookActionCustomerGridQueryBuilderModifier($params)
    {
        $searchQueryBuilder = $params['search_query_builder'];

        $searchCriteria = $params['search_criteria'];

        $searchQueryBuilder->addSelect('IF(dcur.`is_allowed_for_review` IS NULL,0,dcur.`is_allowed_for_review`) AS `is_allowed_for_review`');

        $searchQueryBuilder->leftJoin(
            'c',
            '`' . pSQL(_DB_PREFIX_) . 'ps_customer_reviewer',
            'dcur',
            'dcur . `id_customer` = c.`id_customer`'
        );

        if('is_allowed_for_review' === $searchCriteria->getOrderBy()) {
            $searchQueryBuilder->orderBy('dcur.`is_allowed_for_review`' , $searchCriteria->getOrderWay());
        }

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('is_allowed_for_review' === $filterName) {
                $searchQueryBuilder->andWhere('ducr.`is_allowed_for_review` = :is_allowed_for_review');
                $searchQueryBuilder->setParameter('is_allowed_for_review', $filterValue);

                if(!$filterValue) {
                    $searchQueryBuilder->orWhere('dcur.`is_allowed_for_review` IS NULL');
                }
            }
        }

    }


}