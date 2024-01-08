@extends('user.layouts.app')

@section('main-content')
<!-- contents -->
<div id="contents" class="normal">


    <form>


        <h3>【区分、商品・サービス名】</h3>

        <table class="normal_b eol column1">
            <tr>
                <th>区分</th>
                <th>商品名</th>
            </tr>
            <tr>
                <td nowrap rowspan="3">第32類</td>
                <td>瓶詰ジュース</td>
            </tr>
            <tr>
                <td>保存可能ジュース</td>
            </tr>
            <tr>
                <td>冷凍可能ジュース</td>
            </tr>
            <tr>
                <td nowrap>第20類</td>
                <td>コルク製栓 プラスチック製栓 プラスチック製ふた 木製栓 木製ふた</td>
            </tr>
            <tr>
                <td nowrap>第6類</td>
                <td>ゴム製栓 ゴム製ふた</td>
            </tr>
        </table>

        <font color="orange">※PG:区分の番号の昇順で表示。（2019/05/21）</font>


    </form>

</div><!-- /contents -->
@endsection