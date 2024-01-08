<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="min-width: 80%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body pt-5">
                <div id="contents">
                    <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                    <h3>{{ __('labels.list_change_address.cart.text_3') }}</h3>
                    <div class="row clearfix">
                        <div class="col col-lg-4 mb-4">
                            <table class="normal_b w-100">
                                <caption>
                                    <strong>
                                        <span class="blue fs12">パックC（3商品名まで）</span><br />
                                        出願手続きと登録手続きに加え、拒絶理由通知対応がセットになったプランです。<br />
                                        4商品名以降、追加3商品名ごとに手数料<span id="price_one_block_pack_c"></span>円が発生します。
                                    </strong>
                                </caption>
                                <tr>
                                    <td>
                                        <span class="total_product"></span>商品名<br />
                                        内訳：<br />
                                        基本手数料（3商品名まで）　<span id="price_package_c"></span>円<br />
                                        <span class="product_add"></span>商品名追加手数料（追加3商品名ごと<span id="price_product_other_package_c"></span>円）
                                        <span id="total_price_product_other_package_c"></span>円
                                    </td>
                                    <td class="right"><span class="total_price_package_c"></span>円</td>
                                </tr>
                                <tr>
                                    <th class="right"><strong><span class="total_product"></span>商品名　小計</strong></th>
                                    <th class="right"><strong><span class="total_price_package_c"></span>円</strong></th>
                                </tr>
                                <tr>
                                    <th class="right" colspan="2">
                                        内訳：実手数料　<span id="price_real_c"></span>円<br />
                                        消費税（{{ $setting->value }}％）　<span id="price_tax_c"></span>円</th>
                                </tr>
                            </table>
                        </div><!-- /columne3 -->

                        <div class="col col-lg-4 mb-4">
                            <table class="normal_b w-100">
                                <caption>
                                    <strong>
                                        <span class="blue fs12">パックB（3商品名まで）</span><br />
                                        出願手続きと登録手続きがセットになったプランです。<br />
                                        4商品名以降、追加3商品名ごとに手数料<span id="price_one_block_pack_b"></span>円が発生します。
                                    </strong>
                                </caption>
                                <tr>
                                    <td>
                                        <span class="total_product"></span>商品名<br />
                                        内訳：<br />
                                        基本手数料（3商品名まで）　<span id="price_package_b"></span>円<br />
                                        <span class="product_add"></span>商品名追加手数料（追加3商品名ごと<span id="price_product_other_package_b"></span>円）
                                        <span id="total_price_product_other_package_b"></span>円
                                    </td>
                                    <td class="right"><span class="total_price_package_b"></span>円</td>
                                </tr>
                                <tr>
                                    <th class="right"><strong><span class="total_product"></span>商品名　小計</strong></th>
                                    <th class="right"><strong><span class="total_price_package_b"></span>円</strong></th>
                                </tr>
                                <tr>
                                    <th class="right" colspan="2">
                                        内訳：実手数料　<span id="price_real_b"></span>円<br />
                                        消費税（{{ $setting->value }}％）　<span id="price_tax_b"></span>円</th>
                                </tr>
                            </table>
                        </div><!-- /columne3 -->

                        <div class="col col-lg-4 mb-4">
                            <table class="normal_b w-100">
                                <caption>
                                    <strong>
                                        <span class="blue fs12">パックA（3商品名まで）</span><br />
                                        出願手続きのみのプランです。<br />
                                        4商品名以降、追加3商品名ごとに手数料<span id="price_one_block_pack_a"></span>円が発生します。
                                    </strong>
                                </caption>
                                <tr>
                                    <td>
                                        <span class="total_product"></span>商品名<br />
                                        内訳：<br />
                                        基本手数料（3商品名まで）　
                                        <span id="price_package_a"></span>円<br />
                                        <span class="product_add"></span>商品名追加手数料（追加3商品名ごと<span id="price_product_other_package_a"></span>円）
                                        <span id="total_price_product_other_package_a"></span>円
                                    </td>
                                    <td class="right"><span class="total_price_package_a"></span>円</td>
                                </tr>
                                <tr>
                                    <th class="right"><strong><span class="total_product"></span>商品名　小計</strong></th>
                                    <th class="right"><strong><span class="total_price_package_a"></span>円</strong></th>
                                </tr>
                                <tr>
                                    <th class="right" colspan="2">
                                        内訳：実手数料　<span id="price_real_a"></span>円<br />
                                        消費税（{{ $setting->value }}％）　<span id="price_tax_a"></span>円</th>
                                </tr>
                            </table>
                        </div><!-- /columne3 -->
                    </div><!-- /clearfix -->
                    <ul class="footerBtn eol clearfix">
                        <li>
                            <input type="button" value="閉じる" class="btn_a" data-dismiss="modal"/>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    totalProduct = 0;
    const setting = @JSON($setting);
    const pricePackage = @JSON($pricePackage);

    // Variable other
    countChunkListProduct = 0;
    const block = 3;
    const totalProductBox = $('.total_product');
    const productAdd = $('.product_add');

    function formatPrice(number, unit = '') {
        return Intl.NumberFormat().format(number) + unit;
    }

    function pricePreview() {
        let totalAdditionList = $('#additional-list').find('tr[data-prod_id]').length;
        let totalSuggestList = $('#list-suggest-product').find('tr[data-prod_id]').length;
        totalProduct = totalAdditionList + totalSuggestList;

        updatePrice();
        updatePackA();
        updatePackB();
        updatePackC();
    }

    function updatePrice() {
        totalProductBox.html(totalProduct);

        if (totalProduct > block) {
            productAdd.html(totalProduct - block);

            countChunkListProduct = (totalProduct / block);
            countChunkListProduct = Math.ceil(countChunkListProduct);
        } else {
            productAdd.html(0);
            countChunkListProduct = 0;
        }
    }

    // Pack A
    function updatePackA() {
        const priceOneBlockPackA = $('#price_one_block_pack_a');
        const priceProductPackageA = $('#price_package_a');
        const priceProductOtherPackageA = $('#price_product_other_package_a')
        const totalPriceProductOtherPackageAId = $('#total_price_product_other_package_a');
        const totalPricePackageA = $('.total_price_package_a');
        const priceRealA = $('#price_real_a');
        const priceTaxA = $('#price_tax_a');

        let packBasePrice = pricePackage[0][0]['base_price'];
        let packOneBlock = pricePackage[1][0]['base_price'];
        let packBlock = (countChunkListProduct > 1) ? (countChunkListProduct - 1) * packOneBlock : 0;
        let totalPrice = packBasePrice + packBlock;

        let packBasePriceTax = packBasePrice + Math.ceil((packBasePrice * setting.value) / 100);
        let packOneBlockTax = packOneBlock + Math.ceil((packOneBlock * setting.value) / 100);
        let packBlockTax = packBlock + Math.ceil((packBlock * setting.value) / 100);
        let totalPriceTax = totalPrice + Math.ceil((totalPrice * setting.value) / 100);
        let priceTax = Math.ceil((totalPrice * setting.value) / 100);

        priceOneBlockPackA.text( formatPrice(packOneBlockTax) );
        priceProductPackageA.text( formatPrice(packBasePriceTax) );
        priceProductOtherPackageA.text( formatPrice(packOneBlockTax) );
        totalPriceProductOtherPackageAId.text( formatPrice(packBlockTax) );
        totalPricePackageA.text( formatPrice(totalPriceTax) );
        priceRealA.text( formatPrice(totalPrice) );
        priceTaxA.text( formatPrice(priceTax) );
    }

    // Pack B
    function updatePackB() {
        const priceOneBlockPackB = $('#price_one_block_pack_b');
        const priceProductPackageB = $('#price_package_b');
        const priceProductOtherPackageB = $('#price_product_other_package_b')
        const totalPriceProductOtherPackageBId = $('#total_price_product_other_package_b');
        const totalPricePackageB = $('.total_price_package_b');
        const priceRealB = $('#price_real_b');
        const priceTaxB = $('#price_tax_b');

        let packBasePrice = pricePackage[0][1]['base_price'];
        let packOneBlock = pricePackage[1][1]['base_price'];
        let packBlock = (countChunkListProduct > 1) ? (countChunkListProduct - 1) * packOneBlock : 0;
        let totalPrice = packBasePrice + packBlock;

        let packBasePriceTax = packBasePrice + Math.ceil((packBasePrice * setting.value) / 100);
        let packOneBlockTax = packOneBlock + Math.ceil((packOneBlock * setting.value) / 100);
        let packBlockTax = packBlock + Math.ceil((packBlock * setting.value) / 100);
        let totalPriceTax = totalPrice + Math.ceil((totalPrice * setting.value) / 100);
        let priceTax = Math.ceil((totalPrice * setting.value) / 100);

        priceOneBlockPackB.text( formatPrice(packOneBlockTax) );
        priceProductPackageB.text( formatPrice(packBasePriceTax) );
        priceProductOtherPackageB.text( formatPrice(packOneBlockTax) );
        totalPriceProductOtherPackageBId.text( formatPrice(packBlockTax) );
        totalPricePackageB.text( formatPrice(totalPriceTax) );
        priceRealB.text( formatPrice(totalPrice) );
        priceTaxB.text( formatPrice(priceTax) );
    }

    // Pack C
    function updatePackC() {
        const priceOneBlockPackC = $('#price_one_block_pack_c');
        const priceProductPackageC = $('#price_package_c');
        const priceProductOtherPackageC = $('#price_product_other_package_c')
        const totalPriceProductOtherPackageCId = $('#total_price_product_other_package_c');
        const totalPricePackageC = $('.total_price_package_c');
        const priceRealC = $('#price_real_c');
        const priceTaxC = $('#price_tax_c');

        let packBasePrice = pricePackage[0][2]['base_price'];
        let packOneBlock = pricePackage[1][2]['base_price'];
        let packBlock = (countChunkListProduct > 1) ? (countChunkListProduct - 1) * packOneBlock : 0;
        let totalPrice = packBasePrice + packBlock;

        let packBasePriceTax = packBasePrice + Math.ceil((packBasePrice * setting.value) / 100);
        let packOneBlockTax = packOneBlock + Math.ceil((packOneBlock * setting.value) / 100);
        let packBlockTax = packBlock + Math.ceil((packBlock * setting.value) / 100);
        let totalPriceTax = totalPrice + Math.ceil((totalPrice * setting.value) / 100);
        let priceTax = Math.ceil((totalPrice * setting.value) / 100);

        priceOneBlockPackC.text( formatPrice(packOneBlockTax) );
        priceProductPackageC.text( formatPrice(packBasePriceTax) );
        priceProductOtherPackageC.text( formatPrice(packOneBlockTax) );
        totalPriceProductOtherPackageCId.text( formatPrice(packBlockTax) );
        totalPricePackageC.text( formatPrice(totalPriceTax) );
        priceRealC.text( formatPrice(totalPrice) );
        priceTaxC.text( formatPrice(priceTax) );
    }
</script>
