<form class="form-horizontal addSeigoInvoice" enctype="multipart/form-data">
    <div class="input-group">
        <input type="file" name="file" class="inputFileInvoice" style="display: none;" accept="application/pdf">
        {if isset($filename) && !empty($filename)}
            <button type="button" class="btn btn-danger removeIvoice">
                {l s='Usuń Fakturę' d='Admin.Global'}
            </button>
        {else}
            <button type="button" class="btn btn-action btnFileInvoie">
                <i class="material-icons" aria-hidden="true">note</i>
                {l s='Dodaj Fakturę' d='Admin.Global'}
            </button>
        {/if}
    </div>
</form>
{if isset($filename) && !empty($filename)}
    <div class="form-horizontal downloadFileInvoice">
        <a href="{$filePath}" class="btn btn-success">
            <i class="material-icons" aria-hidden="true">note</i>
            {l s='Pobierz Fakturę' d='Admin.Global'}
        </a>
    </div>
{/if}