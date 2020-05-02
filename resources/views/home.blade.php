<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>Web so sánh</title>
        <link
            rel="stylesheet"
            href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
            integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
            crossorigin="anonymous"
        />
        <link rel="stylesheet" href="/css/app.css" />
    </head>
    <body>
        <div class="container">
            <div class="search-box">
                <h1 style="text-align: center;">WEB SO SÁNH</h1>
                <form action="#" method="GET">
                    <div class="row">
                        <div class="col-6">
                            <input
                                class="form-control"
                                name="q"
                                placeholder="Nhập từ khóa..."
                            />
                        </div>
                        <div class="col-3">
                            <select class="form-control" name="sort" id="sort">
                                <option value="">Mặc định</option>
                                <option value="price-asc">Giá tăng dần</option>
                                <option value="price-desc">Giá giảm dần</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <button class="btn btn-primary btn-block" type="submit">
                                Tìm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="content">
                <div class="row">
                    @foreach ($data as $item)
                    <div class="col-3 {{$item->source}}">
                        <div class="card">
                            <img src="{{$item->image}}" class="card-img-top" />
                            <div class="card-body">
                                <h5 class="card-title">{{$item->title}}</h5>
                                <p class="card-text">
                                    {{number_format($item->price)}}đ
                                </p>
                                <a
                                    href="{{$item->link}}"
                                    target="blank"
                                    class="btn btn-primary"
                                    >Chi tiết >></a
                                ><br />
                                <small>{{$item->source}}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <script
            src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
            integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
            crossorigin="anonymous"
        ></script>
        <script
            src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"
        ></script>
        <script
            src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
            integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
