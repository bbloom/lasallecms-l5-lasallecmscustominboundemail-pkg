@extends('lasallecmsadmin::bob1.layouts.default')

@section('content')
    <!-- Main content -->
    <section class="content">

        <div class="container">

            {{-- form's title --}}
            <div class="row">
                <br /><br />
                {!! $HTMLHelper::adminPageTitle('LaSalleCMS', 'Order Numbers', '') !!}

                @if ( isset($user) )
                    {!! $HTMLHelper::adminPageSubTitle($orderNumber, 'Order Numbers') !!}
                @else
                    {!! $HTMLHelper::adminPageSubTitle(null, 'Order Numbers') !!}
                @endif
            </div> <!-- row -->

            <br /><br />

<h1>hello from admin/order_number/create!!!!!!!</h1>



        </div> <!-- container -->

    </section>
@stop
