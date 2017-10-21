(function () {
    /**
     * html5 上传压缩图片后上传
     * 大于1M自动压缩1-4s 小于1M立即显示
     * @author demo
     */
    window.compressUploadImage = {
        fileUploadInputID: '',
        canvasID: '',
        maxWidth: 1024,
        quality: 0.5,
        details: [],
        imageBase64: '',
        postUrl: '',
        onCompressComplete: function (e) {
        },
        onUploadComplete: function (e) {
        },
        onUploadError: function (e) {
        },
        onUploadProgress:function(e) {
        },
        /**
         * 设置参数
         * @param fileUploadInputID 上传文件input的ID
         * @param canvasID 隐藏的canvasID
         * @param maxWidth 压缩允许的最大宽度
         * @param quality 压缩质量
         * @param postUrl 上传地址
         */
        setParam: function (fileUploadInputID, canvasID, maxWidth, quality, postUrl) {
            this.fileUploadInputID = fileUploadInputID;
            this.canvasID = canvasID;
            this.maxWidth = maxWidth;
            this.quality = quality;
            this.postUrl = postUrl;
        },
        /**
         * 接口：压缩
         */
        compressApply: function () {
            var self  = this;
            document.getElementById(self.fileUploadInputID).removeEventListener('change',function(){},false);
            document.getElementById(self.fileUploadInputID).addEventListener('change',function(){
                self.fileSelected(this);
                self.compressImage(this);
            },false);
        },
        /**
         * 接口：上传
         */
        uploadApply: function (formData) {
            this.uploadFile(formData);
        },
        /**
         * 选择上传的文件
         */
        fileSelected: function (e) {
            var self = this;
            var file = e.files[0];
            var fileSize = self.fileSize(file.size, 'image');
            self.details['filename'] = file.name.length>50?('...'+file.name.substr(-50)):file.name;
            self.details['originSize'] = fileSize[0]+' '+fileSize[1];
            self.details['gt1M'] = fileSize[1]=='M'?1:0;
            self.details['fileType'] = file.type;
        },
        fileSize: function (size, type) {
            var fileSize, suffix;
            if (size > 1024 * 1024) {
                fileSize = Math.round((size * 100 / (1024 * 1024)) / 100);
                suffix = 'M';
            } else {
                fileSize = Math.round((size * 100 / 1024) / 100);
                suffix = 'K';
            }
            return [(type == 'image' ? fileSize : (fileSize * 3 / 4)),suffix];
        },
        /**
         * 上传文件
         */
        uploadFile: function (formData) {
            var self = this;
            var fd = new FormData();
            fd.append('imageBase64', self.imageBase64);
            for(var i in formData){
                fd.append(formData[i].name, formData[i].value);
            }
            var xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', self.onUploadProgress, false);
            xhr.addEventListener('load', self.onUploadComplete, false);
            xhr.addEventListener('error', self.onUploadError, false);
            xhr.addEventListener('abort', self.onUploadError, false);
            xhr.open('post', self.postUrl, true);
            xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
            xhr.send(fd);
        },
        /**
         * 压缩图片然后显示
         * @param e
         */
        compressImage: function (e) {
            var self = this;
            var file = e.files[0];
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(e){
                var url = this.result; //base64
                if(self.details['gt1M']==1){//需要压缩
                    var img = new Image();
                    img.onload = function () {
                        //生成比例
                        var width = img.width, height = img.height, scale = width / height;
                        var newWidth = parseInt(self.maxWidth);
                        var newHeight = parseInt(newWidth / scale);
                        //生成canvas
                        //var start = (new Date()).getTime();
                        var canvas = document.getElementById('canvas');
                        var ctx = canvas.getContext('2d');
                        canvas.setAttribute('width', newWidth);
                        canvas.setAttribute('height', newHeight);
                        EXIF.getData(img, function(){
                            var orientation = EXIF.getTag(this, 'Orientation');
                            orientation = orientation?orientation:1;
                            if (orientation > 4) {
                                canvas.setAttribute('width', newHeight);
                                canvas.setAttribute('height', newWidth);
                            }
                            switch (orientation) {
                                case 2:
                                    // horizontal flip
                                    ctx.translate(newWidth, 0);
                                    ctx.scale(-1, 1);
                                    break;
                                case 3:
                                    // 180° rotate left
                                    ctx.translate(newWidth, newHeight);
                                    ctx.rotate(Math.PI);
                                    break;
                                case 4:
                                    // vertical flip
                                    ctx.translate(0, newHeight);
                                    ctx.scale(1, -1);
                                    break;
                                case 5:
                                    // vertical flip + 90 rotate right
                                    ctx.rotate(0.5 * Math.PI);
                                    ctx.scale(1, -1);
                                    break;
                                case 6:
                                    // 90° rotate right
                                    ctx.rotate(0.5 * Math.PI);
                                    ctx.translate(0, -newHeight);
                                    break;
                                case 7:
                                    // horizontal flip + 90 rotate right
                                    ctx.rotate(0.5 * Math.PI);
                                    ctx.translate(newWidth, -newHeight);
                                    ctx.scale(-1, 1);
                                    break;
                                case 8:
                                    // 90° rotate left
                                    ctx.rotate(-0.5 * Math.PI);
                                    ctx.translate(-newWidth, 0);
                                    break;
                            }
                            orientation = null;
                            if(self.needFix()){
                                self.drawImageIOSFix(ctx,img,newWidth,newHeight);
                            }else{
                                ctx.drawImage(img, 0, 0, newWidth, newHeight);
                            }
                            img = null;
                            var base64 = canvas.toDataURL('image/png', self.quality);
                            self.imageBase64 = base64;
                            var newSize = self.fileSize(base64.length, 'string');
                            self.details['newSize'] = newSize[0]+' '+newSize[1];
                            //alert((new Date()).getTime()-start);
                            self.onCompressComplete(self.details);
                        });
                    };
                    img.src = url;
                }else{//不压缩
                    self.imageBase64 = url;
                    self.onCompressComplete(self.details);
                }

            };
        },
        getUrlParam: function (name) {
            var preg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
            var r = window.location.search.substr(1).match(preg);
            if (r != null) return (r[2]);
            return '';
        },
        drawImageIOSFix: function (ctx, img, newWidth,newHeight) {
            var vertSquashRatio = this.detectVerticalSquash(img);
            ctx.drawImage(img, 0, 0, newWidth, newHeight / vertSquashRatio);
        },
        /**
         * 因为 iOS6 & iOS7 在canvas上绘制图片时，默认会垂直挤压图片，所以我们需要检测出图片的挤压比例
         * @param image
         * @returns {number}
         */
        detectVerticalSquash: function (image) {
            var ih = image.height;
            //创建画布
            var cvs = document.createElement('canvas');
            //设置宽度为1
            cvs.width = 1;
            //设置高度为图片高度
            cvs.height = ih;
            //获取描画对象
            var ctx = cvs.getContext('2d');
            //绘制图片
            ctx.drawImage(image, 0, 0);
            //getImageData(int x,int y,int width,int height)：该方法获取canvas上从(x,y)点开始，宽为width、高为height的图片区域的数据，
            //该方法返回的是一个CanvasPixelArray对象，该对象具有width、height、data等属性。
            //data属性为一个数组，该数组每4个元素对应一个像素点。
            var data = ctx.getImageData(0, 0, 1, ih).data;
            // 有图像的像素点位置 检测图像边缘的情况下，此时它被垂直挤压像素的位置
            var sy = 0;
            //透明的像素点位置
            var ey = ih;
            //当前检索的像素点位置
            var py = ih;

            while (py > sy) {
                // data属性为一个数组，该数组每4个元素对应一个像素点。py - 1：最后一个像素点；
                //3：根据RGBA是代表Red（红色） Green（绿色） Blue（蓝色）和 Alpha的色彩空间，得出Alpha在第四个位置（索引是3）
                var alpha = data[(py - 1) * 4 + 3];
                //透明
                if (alpha === 0) {
                    ey = py;
                } else {
                    sy = py;
                }
                // num >> 1 右移一位相当于除2，右移n位相当于除以2的n次方
                //如果检测到了像素则扩大检索范围，如果没有检索到像素则会缩小范围，每次浮动值为检索到像素的点加没有检索到像素点的一半
                py = (ey + sy) >> 1;
            }
            //求出垂直压缩的比例
            var ratio = (py / ih);
            return (ratio === 0) ? 1 : ratio;
        },
        /**
         * ISO 4-8 需要修复
         * @returns {boolean} 如果需要返回true
         */
        needFix: function () {
            // 判断是否 iPhone 或者 iPod
            if ((navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i))) {
                // 判断系统版本号是否大于等于 8
                return Boolean(navigator.userAgent.match(/OS [4-7]_\d[_\d]* like Mac OS/i));
            } else {
                return false;
            }
        }
    };
})();
