<?php 

namespace Controller\Admin\CustomerReviewController;


use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class CustomerReviewController extends FrameworkBundleAdminController{
    public function toggleIsAllowedForReviewActions($customerId)
    {
        return $this->redirectToRoute('admin_customers_index');
    }
}