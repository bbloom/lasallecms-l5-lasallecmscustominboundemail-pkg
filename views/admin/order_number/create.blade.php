@extends('lasallecmsadmin::bob1.layouts.default')

@section('content')
    <!-- Main content -->
    <section class="content">

        <div class="container">

            {{-- form's title --}}
            <div class="row">
                <br /><br />
                {!! $HTMLHelper::adminPageTitle('LaSalleCMS', 'Order Numbers', '') !!}

                {!! $HTMLHelper::adminPageSubTitle(null, 'Order Numbers') !!}
            </div> <!-- row -->

            <br /><br />

            <div class="row">

                @include('lasallecmsadmin::bob1.partials.message')

                <div class="col-md-3"></div>


                <div class="col-md-6">

                    {!! Form::open(['route' => 'admin.customordernumber.store']) !!}


                    {{-- the table! --}}
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <tr>
                            <td>
                                {!! Form::label('order_number', 'Order Number: ') !!}
                            </td>
                            <td>
                                <input type="text" name="order_number" value="">
                            </td>
                        </tr>


                        <tr>
                            <td>{!! Form::label('user_id', 'LaSalle User: ') !!}</td>
                            <td>
                                {!! $repository->singleSelectFromRelatedTableCreate($user_id_field_list) !!}
                            </td>
                        </tr>

                        <tr><td colspan="2"></td></tr>

                        <tr>
                            <td>

                            </td>
                            <td>
                                {!! Form::submit( 'Create Order Number!') !!}

                                {!! $HTMLHelper::back_button('Cancel') !!}
                            </td>
                        </tr>

                    </table>

                    <input type="hidden" name="title" value="">
                    <input name="field_list" type="hidden" value="[{&quot;name&quot;:&quot;composite_title&quot;,&quot;type&quot;:&quot;composite_title&quot;,&quot;fields_to_concatenate&quot;:[&quot;order_number&quot;,&quot;user_id&quot;],&quot;index_skip&quot;:true},{&quot;name&quot;:&quot;id&quot;,&quot;type&quot;:&quot;int&quot;,&quot;info&quot;:false,&quot;index_skip&quot;:false,&quot;index_align&quot;:&quot;center&quot;},{&quot;name&quot;:&quot;order_number&quot;,&quot;type&quot;:&quot;int&quot;,&quot;info&quot;:false,&quot;index_skip&quot;:false,&quot;index_align&quot;:&quot;center&quot;},{&quot;name&quot;:&quot;user_id&quot;,&quot;alternate_form_name&quot;:&quot;LaSalle User&quot;,&quot;type&quot;:&quot;related_table&quot;,&quot;related_table_name&quot;:&quot;users&quot;,&quot;related_namespace&quot;:&quot;Lasallecms\\Usermanagement\\Models&quot;,&quot;related_model_class&quot;:&quot;User&quot;,&quot;related_fk_constraint&quot;:false,&quot;related_pivot_table&quot;:false,&quot;nullable&quot;:true,&quot;info&quot;:&quot;Required!&quot;,&quot;index_skip&quot;:false,&quot;index_align&quot;:&quot;center&quot;},{&quot;name&quot;:&quot;title&quot;,&quot;type&quot;:&quot;varchar&quot;,&quot;info&quot;:&quot;For internal use only..&quot;,&quot;index_skip&quot;:true,&quot;persist_wash&quot;:&quot;title&quot;}]">
                    <input name="namespace_formprocessor" type="hidden" value="Lasallecms\Lasallecmscustominboundemail\Formprocessing">
                    <input name="classname_formprocessor_create" type="hidden" value="CreateCustomOrderNumberFormProcessing">
                    <input name="crud_action" type="hidden" value="create">


                    </form>

                </div> <!-- col-md-6 -->
                <div class="col-md-3"></div>

            </div> <!-- row -->

        </div> <!-- container -->

    </section>
@stop
