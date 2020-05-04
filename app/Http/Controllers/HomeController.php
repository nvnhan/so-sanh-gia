<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use stdClass;

class HomeController extends Controller
{
    //
    public function index(Request $request)
    {
        $q = $request->q;
        $sort = $request->sort;
        $data = [];

        if (!empty($q)) {
            $data = array_merge(
                self::get_nguyen_kim_data(urlencode($q), $sort),
                self::get_shopee_data(urlencode($q), $sort),
                self::get_sendo_data(urlencode($q), $sort)
            );
            if (empty($sort)) shuffle($data);
            else if ($sort == 'price-asc') usort($data, function ($a, $b) { return $a->price > $b->price; });
        }

        return view('home', compact('data', 'q', 'sort'));
    }

    public function get_nguyen_kim_data(string $q, $sort = '')
    {
        $nguyenkim_url = "https://www.nguyenkim.com/tim-kiem.html?q=$q&sort_by=relevance&sort_order=asc";
        if ($sort == 'price-asc')
            $nguyenkim_url = "https://www.nguyenkim.com/tim-kiem.html?q=$q&sort_by=price&sort_order=asc";
        else if ($sort == 'price-desc')
            $nguyenkim_url = "https://www.nguyenkim.com/tim-kiem.html?q=$q&sort_by=price&sort_order=desc";

        // Document here: https://simplehtmldom.sourceforge.io/manual.htm
        $content = file_get_html($nguyenkim_url);
        $doms = $content->find('[id=pagination_contents] [class$=nk-new-layout-product-grid]');
        $nguyenkim_parse_data = [];
        $cnt = 0;
        foreach ($doms as $key => $value) {
            $tmp = new stdClass;
            $tmp->source = 'nguyenkim';
            $tmp->link = $value->find('a', 0)->href;
            $tmp->title = $value->find('p', 0)->plaintext;

            $data = $value->find('input', 0);
            $tmp->image = $data->getAttribute('data-imgurl');
            $tmp->price = $data->getAttribute('data-price');
            // $tmp->description = $data->getAttribute('data-description');
            $tmp->quantity = $data->getAttribute('data-quantity');
            $tmp->brand = $data->getAttribute('data-brand');
            // $tmp->category = $data->getAttribute('data-category');

            $nguyenkim_parse_data[] = $tmp;
            $cnt++;
            if ($cnt == 20) break;
        }
        return $nguyenkim_parse_data;
    }

    public function get_shopee_data(string $q, $sort = '')
    {
        $shopee_url = "https://shopee.vn/api/v2/search_items/?keyword=$q";
        if ($sort == 'price-asc')
            $shopee_url = "https://shopee.vn/api/v2/search_items/?keyword=$q&by=price&order=asc";
        else if ($sort == 'price-desc')
            $shopee_url = "https://shopee.vn/api/v2/search_items/?keyword=$q&by=price&order=desc";

        // make request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $shopee_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');

        // Bắt buộc phải có referer thì API mới trả về đúng
        $query = explode('?', $shopee_url)[1];
        curl_setopt($ch, CURLOPT_REFERER, "https://shopee.vn/search?" . $query);
        $output = curl_exec($ch);

        $data = [];
        $cnt = 0;
        // handle error; error output
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
            $output = json_decode($output);
            foreach ($output->items as $key => $value) {
                $tmp = new stdClass;
                $tmp->source = 'shopee';
                $tmp->link = "https://shopee.vn/nvnhận-i.$value->shopid.$value->itemid";
                $tmp->title = $value->name;

                $tmp->image = "https://cf.shopee.vn/file/" . $value->image;
                $tmp->price = $value->price_min / 1e5;
                $tmp->quantity = $value->stock;
                $tmp->brand = $value->brand;

                $data[] = $tmp;
                $cnt++;
                if ($cnt == 20) break;
            }
        }

        curl_close($ch);
        return $data;
    }

    public function get_sendo_data(string $q, $sort = '')
    {
        $sendo_url = "https://www.sendo.vn/m/wap_v2/search/product?q=$q&sortType=rank";
        if ($sort == 'price-asc')
            $sendo_url = "https://www.sendo.vn/m/wap_v2/search/product?q=$q&sortType=price_asc";
        else if ($sort == 'price-desc')
            $sendo_url = "https://www.sendo.vn/m/wap_v2/search/product?q=$q&sortType=price_desc";

        // make request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sendo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');

        $output = curl_exec($ch);

        $data = [];
        $cnt = 0;
        // handle error; error output
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
            $output = json_decode($output);
            foreach ($output->result->data as $key => $value) {
                $tmp = new stdClass;
                $tmp->source = 'sendo';
                $tmp->link = "https://www.sendo.vn/$value->cat_path";
                $tmp->title = $value->name;

                $tmp->image = $value->image;
                $tmp->price = $value->price;
                $tmp->quantity = 99; //$value->stock;
                $tmp->brand = ""; //$value->brand;

                $data[] = $tmp;
                $cnt++;
                if ($cnt == 20) break;
            }
        }

        curl_close($ch);
        return $data;
    }
}
