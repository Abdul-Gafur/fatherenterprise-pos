@php
    $custom_labels = json_decode(session('business.custom_labels'), true);
    $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : __('lang_v1.product_custom_field1');
    $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : __('lang_v1.product_custom_field2');
    $product_custom_field3 = !empty($custom_labels['product']['custom_field_3']) ? $custom_labels['product']['custom_field_3'] : __('lang_v1.product_custom_field3');
    $product_custom_field4 = !empty($custom_labels['product']['custom_field_4']) ? $custom_labels['product']['custom_field_4'] : __('lang_v1.product_custom_field4');
@endphp
<table class="table table-bordered table-striped" id="stock_report_table">
    <thead>
        <tr>
            <th>@lang('messages.action')</th>
            <th>SKU</th>
            <th>@lang('business.product')</th>
            <th>@lang('lang_v1.variation')</th>
            <th>@lang('product.category')</th>
            <th>@lang('sale.location')</th>
            <th>@lang('purchase.unit_selling_price')</th>
            <th>@lang('report.current_stock')</th>
            @can('view_product_stock_value')
                <th class="stock_price">@lang('lang_v1.total_stock_price')
                    <br><small>(@lang('lang_v1.by_purchase_price'))</small></th>
                <th>@lang('lang_v1.total_stock_price') <br><small>(@lang('lang_v1.by_sale_price'))</small></th>
                <th>@lang('lang_v1.potential_profit')</th>
            @endcan
            <th>@lang('report.total_unit_sold')</th>
            <th>@lang('lang_v1.total_unit_transfered')</th>
            <th>@lang('lang_v1.total_unit_adjusted')</th>
            <th>{{$product_custom_field1}}</th>
            <th>{{$product_custom_field2}}</th>
            <th>{{$product_custom_field3}}</th>
            <th>{{$product_custom_field4}}</th>
            @if($show_manufacturing_data)
                <th class="current_stock_mfg">@lang('manufacturing::lang.current_stock_mfg')
                    @show_tooltip(__('manufacturing::lang.mfg_stock_tooltip'))</th>
            @endif
        </tr>
    </thead>
    <tfoot>
        <tr class="bg-gray font-17 text-center footer-total">
            <td colspan="7"><strong>@lang('sale.total'):</strong></td>
            <td><strong>@lang('report.current_stock'):</strong> <span class="footer_total_stock"></span></td>
            @can('view_product_stock_value')
                <td><strong>@lang('lang_v1.total_stock_price'):</strong> <span class="footer_total_stock_price"></span></td>
                <td><strong>@lang('lang_v1.total_stock_price'):</strong> <span
                        class="footer_stock_value_by_sale_price"></span></td>
                <td><strong>@lang('lang_v1.potential_profit'):</strong> <span class="footer_potential_profit"></span></td>
            @endcan
            <td><strong>@lang('report.total_unit_sold'):</strong> <span class="footer_total_sold"></span></td>
            <td><strong>@lang('lang_v1.total_unit_transfered'):</strong> <span class="footer_total_transfered"></span>
            </td>
            <td><strong>@lang('lang_v1.total_unit_adjusted'):</strong> <span class="footer_total_adjusted"></span></td>
            <td colspan="4"></td>
            @if($show_manufacturing_data)
                <td><strong>@lang('manufacturing::lang.current_stock_mfg'):</strong> <span
                        class="footer_total_mfg_stock"></span></td>
            @endif
        </tr>
    </tfoot>
</table>