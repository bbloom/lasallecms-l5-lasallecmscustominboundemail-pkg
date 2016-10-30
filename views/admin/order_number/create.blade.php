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

            <div class="row">

                @include('lasallecmsadmin::bob1.partials.message')

                <div class="col-md-3"></div>

                <div class="col-md-6">



                </div> <!-- col-md-6 -->
                <div class="col-md-3"></div>


                    {{-- this is a combo create or edit form. Display the proper "form action"  --}}
                    @if ( isset($orderNumber) )
                        {!! Form::model($orderNumber, array('route' => array('admin.customordernumber.update', $orderNumber->id), 'method' => 'PUT')) !!}

                        {!! Form::hidden('id', $orderNumber->id) !!}
                    @else
                        {!! Form::open(['route' => 'admin.customordernumber.store']) !!}
                    @endif

                    {{-- the table! --}}
                    <table class="table table-striped table-bordered table-condensed table-hover">

                        <tr>
                            <td>

                            </td>
                            <td>
                                @if ( isset($orderNumber) )
                                    {!! Form::submit( 'Edit Order Number!') !!}
                                @else
                                    {!! Form::submit( 'Create Order Number!') !!}
                                @endif

                                {!! $HTMLHelper::back_button('Cancel') !!}
                            </td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>

                        <tr>
                            <td>
                                {!! Form::label('order_number', 'Order Number: ') !!}
                            </td>
                            <td>

                                @if ( isset($orderNumber) )
                                    {!! Form::test('email', Input::old('order_number', isset($orderNumber) ? $orderNumber->order_number : '')) !!}&nbsp;&nbsp; <a href="#" data-toggle="popover" data-content="Order Number must be unique."><i class="fa fa-info-circle"></i></a>
                                    {{{ $errors->first('order_number', ':message') }}}
                                @else
                                    <input type="text" name="order_number" value="{{{ $orderNumber->order_number }}}" disabled>
                                @endif
                            </td>
                        </tr>



                        @if ( isset($orderNumber) )
                            <input type="hidden" name="title" value="{{{ $orderNumber->title }}}">
                        @endif

                        </table>

            </div> <!-- row -->

        </div> <!-- container -->

    </section>
@stop
