{extends file='customer/page.tpl'}

{block name='page_header_container'}
{/block}

{block name='page_content_container'}
    <section id="content" class="page-content">
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered table-labeled hidden-sm-down">
                    <thead class="thead-default">
                        <tr>
                            <th>{l s='Nr zamówienia' d='Admin.Global'}</th>
                            <th>{l s='Faktura' d='Admin.Global'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if isset($invoices)}
                            {foreach from=$invoices item=item}
                                <tr>
                                    <td>{$item.order->reference}</td>
                                    <td>
                                        <a class="btn btn-primary" href="/upload/seigoinvoice/{$item.invoice->filename}" download>
                                            {l s='Pobierz' d='Admin.Global'}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>


            <div class="col-sm-12">
                <form method="POST">
                    <div class="form-group row ">
                        <label class="col-md-3 form-control-label required" for="field-firstname">
                            {l s='Wyślij fakturę na adres email:' d='Admin.Global'}
                        </label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" value="{if isset($email_invoice)}{$email_invoice}{/if}"
                                name="emailInvoice">
                        </div>
                        <footer class="form-footer clearfix">
                            <button class="btn btn-primary form-control-submit float-xs-right" type="submit">
                                {l s='Zapisz' d='Admin.Global'}
                            </button>
                        </footer>
                    </div>
                </form>
            </div>
        </div>
    </section>
{/block}