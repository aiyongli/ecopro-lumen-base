<?php
namespace Ecopro\Base\Concerns;

use Ecopro\Base\Helpers\InstanceHelper;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

trait Paginate
{
    /**
     * 设置分页请求接收参数中的单页偏移量参数
     * @return static
     */
    public function autoPerPage()
    {
        $perPageName = PAGINATE_PAGE_SIZE_NAME;
        $this->perPage = InstanceHelper::singleton(Request::class)->input($perPageName, isset($this->perPage) ? $this->perPage : 15);

        return $this;
    }

    /**
     * 设置分页请求接收参数中的页码参数
     * @return static
     */
    public function autoPageName()
    {
        Paginator::currentPageResolver(function ($defaultPageName = 'page') {
            $pageName = PAGINATE_PAGE_NAME;
            $page = InstanceHelper::singleton(Request::class)->input($pageName);

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return (int) $page;
            }

            return 1;
        });

        return $this;
    }

    /**
     * 设置分页请求接收参数
     * @return static
     */
    public function autoPaginateParams()
    {
        return $this->autoPageName()->autoPerPage();
    }
}
