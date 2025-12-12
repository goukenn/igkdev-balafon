// @ts-nocheck
"use strict";

// @ts-ignore
igk.ready(function () { 
    $igk('form').each_all(function () {
        let f = $igk(this);
        let MAX_UPLOAD_FILE = 0;
        let MAX_FILE_SIZE = 0;       
        let files = f.qselect('input[type="file"]');
        let q = f.qselect('#MAX_UPLOAD_FILE').first();
        let max_size = f.qselect('#MAX_FILE_SIZE').first();
        if (q && (files.getCount() > 0)) {
            MAX_UPLOAD_FILE = parseInt(q.o.value);
            if (MAX_UPLOAD_FILE > 0) {
                // activate the file upload detected 
                if (max_size) {
                    
                    files.each_all(function () {
                        let i = this;
                        i.reg_event('change', function (e) {
                            MAX_FILE_SIZE = parseInt(max_size.o.value);;
                            if (MAX_FILE_SIZE){
                                for(let _ti of this.files){
                                    if (_ti.size > MAX_FILE_SIZE){
                                        i.o.value = '';
                                        break;
                                    }
                                }
                            }
                        });
                    });
                }

                f.reg_event('submit', function (e) {
                    MAX_UPLOAD_FILE = parseInt(q.o.value);
                    // console.log("on submit prevent max size"); 
                    let count = 0;
                    files.each_all(function () {
                        let i = this;
                        count += i.o.files.length;
                    });
                    if ((MAX_UPLOAD_FILE > 0) && (count > MAX_UPLOAD_FILE)) {
                        e.preventDefault();
                        f.qselect(".form-prevent-max-upload").first().toggleClass('dispn');
                    }
                });
            }
        };


    });
});
