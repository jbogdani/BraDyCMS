/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 */

var _ignorehash;

var admin = {

  wysiwyg: {
    add: function(id, opts){

      opts = opts || {};

      var default_opts = {
        selector: '#' + id,
        content_css: './sites/default/css/styles.css',
        plugins: [
         "advlist autolink link image lists charmap preview hr anchor",
         "searchreplace wordcount visualblocks visualchars code fullscreen media nonbreaking",
         "save table directionality paste template imagetools importcss"
        ],
        importcss_append: true,
        importcss_groups: [{title: 'Other styles'}],
        paste_as_text: true,
        toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image anchor | print preview media fullpage | forecolor backcolor | code",
        image_advtab: true,
        removed_menuitems: 'newdocument',
        templates: [
          {title: 'download file', content: '[[dwnl file=""]]text[[/dwnl]]'},
          {title: 'fig', content: '[[fig path="./sites/default/images/"]]caption[[/fig]]'},
          {title: 'link', content: '[[link]]editme[[/link]]'},
          {title: 'download-node', content: '[[download]]editme[[/download]]'},
          {title: 'gallery', content: '[[gallery]]editme[[/gallery]]'},
          {title: 'map', content: '[[map]]editme[[/map]]'},
          {title: 'userform', content: '[[userform]]editme[[/userform]]'},
          {title: 'vimeo', content: '[[vimeo]]editme[[/vimeo]]'},
          {title: 'youtube', content: '[[youtube]]editme[[/youtube]]'},
          {title: 'gcalendar', content: '[[gcalendar]]editme[[/gcalendar]]'}
        ]
      };
      $.extend(default_opts, opts);

      tinymce.init(default_opts);

    },

    preSave: function(){

    },

    setValue: function(id, text){
      tinymce.get(id).setContent(text);
    }
  },

  removeNotValid: function(input, opts){
    /**
     * available opts:
     *  replace: string
     *  toLower: boolean
     *  permit: array
     */
     opts = opts || {};

    var pattern = "_,=\.\\s\\?'\"\\-\$:;’°“”«»\(\)";

    if (opts.permit){
      $.each(opts.permit, function(i, item){
        pattern = pattern.replace(item, '');
      });
    }

    input.on('keyup', function(e){
      if (
        //http://www.cambiaresearch.com/articles/15/javascript-char-codes-key-codes
        e.keyCode === 13  || //enter
        e.keyCode === 37  || //left-arrow
        e.keyCode === 38  || //up-arrow
        e.keyCode === 39  ||  //right-arrow
        e.keyCode === 40  ||  //down-arrow
        e.keyCode === 8  ||  //backspace
        e.keyCode === 46  ||  //delete
        e.keyCode === 16  ||  //shift
        e.keyCode === 17  ||  //CTRL
        e.keyCode === 18  ||  //alt
        e.keyCode === 91  ||  //left window
        e.keyCode === 93  //select key
        ){
        return;
      }

      var val = $(this).val();
      if (val && val !== ''){
        var newVal = val
          .replace(new RegExp("([" + pattern + "])", "gi"), opts.replace ? opts.replace : '');
        newVal = opts.toLower ? newVal.toLowerCase() : newVal;
        // if (opts.replace){
        //   newVal = newVal.replace(new RegExp(opts.replace + '+', "gi"), opts.replace)
        //     .replace(new RegExp(opts.replace + '$'), '')
        //     .replace(new RegExp('^' + opts.replace), '');
        // }

        // https://stackoverflow.com/a/18123985/586449
        var defaultDiacriticsRemovalMap = [
          {'base':'A', 'letters':/[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g},
          {'base':'AA','letters':/[\uA732]/g},
          {'base':'AE','letters':/[\u00C6\u01FC\u01E2]/g},
          {'base':'AO','letters':/[\uA734]/g},
          {'base':'AU','letters':/[\uA736]/g},
          {'base':'AV','letters':/[\uA738\uA73A]/g},
          {'base':'AY','letters':/[\uA73C]/g},
          {'base':'B', 'letters':/[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g},
          {'base':'C', 'letters':/[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g},
          {'base':'D', 'letters':/[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g},
          {'base':'DZ','letters':/[\u01F1\u01C4]/g},
          {'base':'Dz','letters':/[\u01F2\u01C5]/g},
          {'base':'E', 'letters':/[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g},
          {'base':'F', 'letters':/[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},
          {'base':'G', 'letters':/[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g},
          {'base':'H', 'letters':/[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g},
          {'base':'I', 'letters':/[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g},
          {'base':'J', 'letters':/[\u004A\u24BF\uFF2A\u0134\u0248]/g},
          {'base':'K', 'letters':/[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g},
          {'base':'L', 'letters':/[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g},
          {'base':'LJ','letters':/[\u01C7]/g},
          {'base':'Lj','letters':/[\u01C8]/g},
          {'base':'M', 'letters':/[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g},
          {'base':'N', 'letters':/[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g},
          {'base':'NJ','letters':/[\u01CA]/g},
          {'base':'Nj','letters':/[\u01CB]/g},
          {'base':'O', 'letters':/[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g},
          {'base':'OI','letters':/[\u01A2]/g},
          {'base':'OO','letters':/[\uA74E]/g},
          {'base':'OU','letters':/[\u0222]/g},
          {'base':'P', 'letters':/[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g},
          {'base':'Q', 'letters':/[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g},
          {'base':'R', 'letters':/[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g},
          {'base':'S', 'letters':/[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g},
          {'base':'T', 'letters':/[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g},
          {'base':'TZ','letters':/[\uA728]/g},
          {'base':'U', 'letters':/[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g},
          {'base':'V', 'letters':/[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g},
          {'base':'VY','letters':/[\uA760]/g},
          {'base':'W', 'letters':/[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g},
          {'base':'X', 'letters':/[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g},
          {'base':'Y', 'letters':/[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g},
          {'base':'Z', 'letters':/[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g},
          {'base':'a', 'letters':/[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g},
          {'base':'aa','letters':/[\uA733]/g},
          {'base':'ae','letters':/[\u00E6\u01FD\u01E3]/g},
          {'base':'ao','letters':/[\uA735]/g},
          {'base':'au','letters':/[\uA737]/g},
          {'base':'av','letters':/[\uA739\uA73B]/g},
          {'base':'ay','letters':/[\uA73D]/g},
          {'base':'b', 'letters':/[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g},
          {'base':'c', 'letters':/[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g},
          {'base':'d', 'letters':/[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g},
          {'base':'dz','letters':/[\u01F3\u01C6]/g},
          {'base':'e', 'letters':/[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g},
          {'base':'f', 'letters':/[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g},
          {'base':'g', 'letters':/[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g},
          {'base':'h', 'letters':/[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g},
          {'base':'hv','letters':/[\u0195]/g},
          {'base':'i', 'letters':/[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g},
          {'base':'j', 'letters':/[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g},
          {'base':'k', 'letters':/[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g},
          {'base':'l', 'letters':/[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g},
          {'base':'lj','letters':/[\u01C9]/g},
          {'base':'m', 'letters':/[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g},
          {'base':'n', 'letters':/[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g},
          {'base':'nj','letters':/[\u01CC]/g},
          {'base':'o', 'letters':/[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g},
          {'base':'oi','letters':/[\u01A3]/g},
          {'base':'ou','letters':/[\u0223]/g},
          {'base':'oo','letters':/[\uA74F]/g},
          {'base':'p','letters':/[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g},
          {'base':'q','letters':/[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g},
          {'base':'r','letters':/[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g},
          {'base':'s','letters':/[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g},
          {'base':'t','letters':/[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g},
          {'base':'tz','letters':/[\uA729]/g},
          {'base':'u','letters':/[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g},
          {'base':'v','letters':/[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g},
          {'base':'vy','letters':/[\uA761]/g},
          {'base':'w','letters':/[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g},
          {'base':'x','letters':/[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},
          {'base':'y','letters':/[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g},
          {'base':'z','letters':/[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g}
        ];

        for(var i=0; i<defaultDiacriticsRemovalMap.length; i++) {
          newVal = newVal.replace(defaultDiacriticsRemovalMap[i].letters, defaultDiacriticsRemovalMap[i].base);
        }

        newVal = newVal.replace(/\-+/g, '-')
              .replace(/^\-/, '')
              .replace(/\-$/, '');
        $(this).val(newVal);
      }
    });

    if (opts.replace){
      input.on('change', function(){
        var val = $(this).val();
        val = val.replace(new RegExp(opts.replace + '+', "gi"), opts.replace)
          .replace(new RegExp(opts.replace + '$'), '')
          .replace(new RegExp('^' + opts.replace), '');
        $(this).val(val);
      });
    }

  },

  /**
   *
   * opts.sizeLimit
   * opts.minSizeLimit
   * opts.one
   * opts.allowedExtensions
   * opts.loaded(id, filename, responseJSON)
   * opts.allLoaded(succeeded, failed)
   */
  upload: function(element, action, opts){

    if($('#qq-template').length < 1){
      var script = $('<script>').attr('type', 'text/template').attr('id', 'qq-template').appendTo('body');
      script.html('<div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">' +
      '    <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">' +
      '        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>' +
      '    </div>' +
      '    <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>' +
      '        <span class="qq-upload-drop-area-text-selector"></span>' +
      '    </div>' +
      '    <div class="qq-upload-button-selector qq-upload-button">' +
      '        <div>Upload a file</div>' +
      '    </div>' +
      '        <span class="qq-drop-processing-selector qq-drop-processing">' +
      '            <span>Processing dropped files...</span>' +
      '            <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>' +
      '        </span>' +
      '    <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">' +
      '        <li>' +
      '            <div class="qq-progress-bar-container-selector">' +
      '                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>' +
      '            </div>' +
      '            <span class="qq-upload-spinner-selector qq-upload-spinner"></span>' +
      '            <span class="qq-upload-file-selector qq-upload-file"></span>' +
      '            <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>' +
      '            <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">' +
      '            <span class="qq-upload-size-selector qq-upload-size"></span>' +
      '            <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>' +
      '            <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>' +
      '            <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>' +
      '            <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>' +
      '        </li>' +
      '    </ul>' +
      '' +
      '    <dialog class="qq-alert-dialog-selector">' +
      '        <div class="qq-dialog-message-selector"></div>' +
      '        <div class="qq-dialog-buttons">' +
      '            <button type="button" class="qq-cancel-button-selector">Close</button>' +
      '        </div>' +
      '    </dialog>' +
      '' +
      '    <dialog class="qq-confirm-dialog-selector">' +
      '        <div class="qq-dialog-message-selector"></div>' +
      '        <div class="qq-dialog-buttons">' +
      '            <button type="button" class="qq-cancel-button-selector">No</button>' +
      '            <button type="button" class="qq-ok-button-selector">Yes</button>' +
      '        </div>' +
      '    </dialog>' +
      '' +
      '    <dialog class="qq-prompt-dialog-selector">' +
      '        <div class="qq-dialog-message-selector"></div>' +
      '        <input type="text">' +
      '        <div class="qq-dialog-buttons">' +
      '            <button type="button" class="qq-cancel-button-selector">Cancel</button>' +
      '            <button type="button" class="qq-ok-button-selector">Ok</button>' +
      '        </div>' +
      '    </dialog>' +
      '</div>');
    }
    opts = opts || {};

    new qq.FineUploader({
      request: {
        endpoint: action
      },
      element: element,
      validation:{
        allowedExtensions: opts.allowedExtensions ? opts.allowedExtensions : [],
        sizeLimit: opts.sizeLimit ? opts.sizeLimit : 0,
        minSizeLimit: opts.minSizeLimit ? opts.minSizeLimit : 0,
      },
      multiple: opts.one ? false : true,
      callbacks:{
        onComplete: function(id, filename, responseJSON){
          return opts.loaded ? opts.loaded(id, filename, responseJSON) : false;
        },
        onAllComplete: function(succeeded, failed){
          return opts.allLoaded ? opts.allLoaded(succeeded, failed) : false;
        }
      }
    });
  },


		/**
		 * Filters elements with class search-container by text contained in search-text
		 * @param string filter	string to use as filter
		 * @param obj container jQuery element to look in
		 * @param boolean parent_class if false search-container and search-text are the same element, otherwise container contains element
		 * @returns {undefined}
		 */
		filterList: function(filter, container, parent_class){

			if (filter && filter !== '') {
				if (parent_class){
					container.find('.search-text:not(:Contains(' + filter + '))').parents('.search-container').fadeOut();
					container.find('.search-text:Contains(' + filter + ')').parents('.search-container').fadeOut();

				} else {

					container.find('.search-text:not(:Contains(' + filter + '))').fadeOut();
					container.find('.search-text:Contains(' + filter + ')').fadeIn();
				}

			} else {
		     container.find( '.' + (parent_class ? 'search-container' : 'search-text') ).fadeIn();
			}
		},

		/**
		 *
		 * @param opts
		 * 		title
		 * 		html
		 * 		loaded
		 * 		buttons: []
		 * 			text
		 * 			href
		 * 			click
		 * 			action: 'close'
		 * 			addclass
     *      glyphicon
		 */
		dialog: function(opts){
			if ($('#modal').length > 0){
				$('#modal').modal('hide');
			}

			var dialog =  $('<div />').attr('id', 'modal').addClass('modal fade').append(
						'<div class="modal-dialog">' +
							'<div class="modal-content">' +
								(opts.title ? '<div class="modal-header"><h4>' + opts. title + '</h4></div>' : '') +
							'</div>' +
						'</div>'

						).appendTo('body');

			if (opts.width){
					dialog.css({
						width:opts.width,
						'margin-left':'-' + (opts.width/2) + 'px'
					});
			}

			var body = $('<div />').addClass('modal-body').appendTo(dialog.find('div.modal-content')),
				URLstring = 'controller.php?';

			if (opts.buttons && typeof opts.buttons == 'object'){

				var footer = $('<div />').addClass('modal-footer').appendTo(dialog.find('div.modal-content'));

				$.each(opts.buttons, function(index, but){
					var a = $('<a />')
            .addClass('btn'+ (but.addclass ? ' ' + but.addclass : ' btn-primary'))
            .html((but.glyphicon ? '<i class="glyphicon glyphicon-' + but.glyphicon + '"></i> ' : '') + but.text);

					if (but.href){
						a.attr('href', but.href);
					}

					if (but.click){
						if (but.click === 'close'){
							a.attr('data-dismiss', 'modal');
						} else{
							a.click(function(){ but.click(dialog); });
						}
					}

					if (but.action === 'close'){
						a.attr('data-dismiss', 'modal');
					}

					a.appendTo(footer);
				});
			}

			dialog.modal({'keyboard':true});

			dialog.on('hidden.bs.modal', function(){
				$('body').removeClass('modal-open');
				dialog.remove();
			});


			if (opts.html){
				body.html(opts.html);

				if (opts.loaded){
					opts.loaded(body);
				}

			} else if (opts.obj && opts.method){
				dialog.find('.modal-body').html('<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw margin-bottom" style="font-size: 3em;"></i>');
				$.ajax({
	        		'type': opts.post ? 'POST' : 'GET',
	        		'url': 'controller.php?obj=' + opts.obj + '&method=' + opts.method + (opts.param ? '&param[]=' + opts.param.join('&param[]=') : '' ),
	        		'data': opts.post
	        	})
	        	.done(function(data){
	        		dialog.find('.modal-body').html(data);
	        		if (opts.loaded){
	        			opts.loaded(dialog);
	        		}
	        	})
            .fail(function(){
              dialog.find('.modal-body').html('<p class="text-danger">' + admin.tr('error_check_log') + '</p>');
            });
			}
		},
		/**
		 *
		 * @param text
		 * @param title
		 * @param type : false, info, success, error
		 * @param sticky
		 */
		message: function(text, type, title, sticky){
      new PNotify({
        title: title ?  title : false,
        text: text,
        type: type ? type : false,
        hide: sticky ? false : true,
        styling: 'bootstrap3'
      });
		},

	tabs : {
	    tab: '',
	    set: function(el){
	        if (typeof el == 'string'){
	            this.tab = $(el);
	        } else {
	            this.tab = el;
	        }
	    },
	    start: function(){
	        tab = this.tab;
	        tab.find('a').click(function (e) {
	            e.preventDefault();
	            $(this).tab('show');
	        });

	        tab.find('button.close').click(function(e){
	            var li = $(this).parents('li');
	            admin.tabs.closeTab(li);
	            return false;
	        });
	    },
	    /**
	     *
	     * @param opts object
	     * 		title
	     *
	     * 		html
	     *
	     * 		obj
	     * 		method
	     */
	    add: function(opts){

	    	var title = opts.title ? opts.title : '',
	    	tab = this.tab,
	    	id = Math.floor(Math.random()*1000) + '' + tab.find('li').length;

	        this.tab.append('<li><a href="#added' + id + '">' + title + '<button class="close" type="button">×</button></a></li>');

	        if (opts.html){
	        	this.tab.next('div.tab-content').append('<div class="tab-pane" id="added' + id + '">' + opts.html + '</div>');
	        	this.start(tab);
        		this.tab.find('li a:last').tab('show');
						this.tab.find('li:last').data('url', location.hash.substring(1));
						this.tab.find('li:last').data('opts', opts);

	        } else if (opts.obj && opts.method){
	        	admin.tabs.tab.next('div.tab-content')
              .append('<div class="tab-pane" id="added' + id + '"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw margin-bottom" style="font-size: 3em;"></i></div>');

	        	admin.tabs.start(tab);

        		admin.tabs.tab.find('li a:last').tab('show');
						this.tab.find('li:last').data('url', location.hash.substring(1));
						this.tab.find('li:last').data('opts', opts);
	        	$.ajax({
	        		'type': opts.post ? 'POST' : 'GET',
	        		'url': 'controller.php?obj=' + opts.obj + '&method=' + opts.method + (opts.param ? '&param[]=' + opts.param.join('&param[]=') : '' ),
	        		'data': opts.post
	        	})
	        	.done(function(data){
	        		$('#added' + id).html(data);
	        	})
            .fail(function(){
              $('#added' + id).html('<p class="text-danger">' + admin.tr('error_check_log') + '</p>');
            });
	        } else {
	        	return false;
	        }
          this.tab.find('li:last').on('click', function(){
            _ignorehash = true;
            location.hash = '#' + $(this).data('url');
          });
	    },

	    reloadActive: function(){
        this.reload();
	    },

      reloadThis: function(el){
        if(!(el instanceof jQuery)){
          el = $(el);
        }
        var index = el.parents('.tab-pane').index();
        if (index){
          this.reload(index);
        }
      },

      reload: function(index){
        var pane, opts;

        if (!index){
          pane = $('div.tab-content div.active');
          opts = tab.find('li.active').data('opts');
        } else {
          pane = $('div.tab-content .tab-pane').get(index);
          pane = $(pane);
          opts = $(tab.find('li').get(index)).data('opts');
        }

	    	var url = 'controller.php?obj=' + opts.obj + '&method=' + opts.method + (opts.param ? '&param[]=' + opts.param.join('&param[]=') : '' );

	    	pane.html('<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw margin-bottom" style="font-size: 3em;"></i>');

        $.ajax({
	        		'url': url,
	        		'data': opts.data
	        	})
	        	.done(function(data){
	        		pane.html(data);
	        	})
            .fail(function(){
              pane.html('<p class="text-danger">' + admin.tr('error_check_log') + '</p>');
            });
	    },

	    closeActive: function(state){
	    	var active = tab.find('li.active');
	    	admin.tabs.closeTab(active, state);
	    },

	    closeTab: function(li, state){

        // only one tab is left, the Welcome tab. This should never be closed!
				if(tab.find('li').length === 1){
					if (state){
            location.hash = '#' + state;
					}
					return;
				}

				// I'm closing the active tab
				if (li.hasClass('active')){

          // if state is defined load new tab and then remove old one
					if (state){
						var actualState = location.hash.substr(1);


            if (actualState === state){
              admin.tabs.reloadActive();
              return;
              //  $(window).trigger( "hashchange" );

            } else {
              location.hash = '#' + state;
              //$(window).trigger( "hashchange" );

              tab.find('li a:last').tab('show');
            }

            // remove tab-pane
            $('#' + li.find('a').attr('href').replace('#', '')).remove();

            // remove li element
            li.remove();

          } else {
            // remove tab-pane
            $('#' + li.find('a').attr('href').replace('#', '')).remove();

            // remove li element
            li.remove();

            tab.find('li a:last').tab('show');

            var prevState = tab.find('li:last').data('url');

            if (prevState && prevState !== 'undefined'){
              _ignorehash = true;
              location.hash = '#' + prevState;
            } else{
              location.hash = '#';
            }
          }
        // I'm closing an not active tab: just remove the tab and do nothing!
				} else {
					li.remove();
				}
	    }
	},

	tr: function (string) {

		return lang[string] ? lang[string] : string;
	},

	media: {
		go2dir: function(dir){
			admin.tabs.closeActive('media/all/' + dir);
		},

		deleteDir: function(path){
			$.get('controller.php?obj=media_ctrl&method=delete&param[]=' + path, function(data){
				admin.message(data.text, data.status);
				if(data.status == 'success'){
					admin.media.go2dir(data.new_path);
				}
			}, 'json');
		},


		deleteFile: function(full_path){
			admin.dialog({
				title: admin.tr('pay_attention_please'),
				html: admin.tr('confirm_delete_file'),
				buttons:[
          {
            text: '<i class="glyphicon glyphicon-trash"></i>  ' + admin.tr('delete'),
            addclass: 'btn-danger',
            action: 'close',
            click: function(){
              $.get('controller.php?obj=media_ctrl&method=delete&param[]=' + full_path, function(data){
                admin.message(data.text, data.status);
                if (data.status == 'success'){
                  admin.tabs.closeActive('media/all/' + data.new_path);
                }
              }, 'json');
            }
          },
          {
            text: admin.tr('close'),
            action: 'close'
          },
        ]
			});

		},

		filterDir: function(filter, uid){
			if (filter) {
		      $('#media-' + uid + ' div.searcheable:not(:Contains(' + filter + '))').parents('li').fadeOut();
		      $('#media-' + uid + ' div.searcheable:Contains(' + filter + ')').parents('li').fadeIn();
		    } else {
		      $('#media-' + uid + ' li').fadeIn();
		    }
		}
	}
};




$(function () {


	admin.tabs.set('#tabs');
	admin.tabs.start();


    $(window).on( 'hashchange', function(e) {

			if (_ignorehash){
				_ignorehash = false;
				return;
			}

    	var url = location.hash.substring(1);

    	if (!url || url.match(/\bnt-/)){

				return;
    	} else {

    		var url_arr = url.split('/');

    		admin.tabs.add({
    			'title': url_arr[0] + '/' + url_arr[1] + (url_arr[2] ? '/' + url_arr[2].substr(0, 5) + '...' : ''),
    			'obj': url_arr[0] + '_ctrl',
    			'method': url_arr[1],
    			'param': url_arr.slice(2)
    		});
    	}
    }).trigger('hashchange');

});


jQuery.expr[':'].Contains = function(a,i,m){
    return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
};

$('a.ftpopover').popover({html:true});

$(document).ready(function () {

  $('.toggleMenu').on('click', function(e){
    $('#menu, #content').toggleClass('shown');
    if ($(this).attr('href') === '#'){
      e.preventDefault();
    }
  });
  $('#content').on('click', function(){
    $('#menu, #content').removeClass('shown');
    $('#menu ul.sub').removeClass('show');
  });

  $('#menu a').on('click', function(){
    if ($(this).attr('href') !== '#'){
      $('#menu ul.sub').removeClass('show');
      $('#menu, #content').removeClass('shown');
    }
  });

  $('#menu a.sub-toggle').on('click', function(e){
    $(this).next('ul.sub').toggleClass('show');
    $('#menu ul.sub').not($(this).next('ul.sub')).removeClass('show');

    e.preventDefault();
    return false;
  });

  if(!('ontouchstart' in document.documentElement)){
    $('[data-toggle="tooltip"]').tooltip();
  }
});
