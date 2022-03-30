var $defaults = {
    selector: "textarea.tinymce",
    browser_spellcheck: true,
    //contextmenu: false,
    language: 'pt_BR',

    plugins: [
        "advlist autosave autolink link image lists charmap preview hr anchor",
        "searchreplace fullscreen paste",
        "table textcolor paste textcolor colorpicker textpattern"
    ],

    toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect forecolor backcolor",
    //toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink | preview | fullscreen",
    //toolbar3: "table | hr removeformat | subscript superscript | charmap | ltr rtl",

    menubar: true,
    toolbar: true,
    statusbar: false,
    toolbar_items_size: 'small',

    paste_data_images: false,
    paste_as_text: true,

    autosave_restore_when_empty: false
};