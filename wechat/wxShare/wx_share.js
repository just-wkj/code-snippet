//微信分享基本配置
var shareConfig = {
    debug: false,//调试开关
    title: 'title',
    link: 'http://wechat.moyixi.cn/article.php',
    imgUrl: 'https://aizuna.house365.com/upload_wx_images/2017/07/16/5277c33097006330074665744427e417.jpg',
    desc: '分享描述',
    request: './wx_share.php'//异步请求地址
};

//微信分享设置
function WxCallBack(wxparam) {
    wx.config({
        debug: shareConfig.debug, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: wxparam.appId, // 必填，公众号的唯一标识
        timestamp: wxparam.timestamp, // 必填，生成签名的时间戳
        nonceStr: wxparam.nonceStr, // 必填，生成签名的随机串
        signature: wxparam.signature,// 必填，签名，见附录1
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo'
        ]
    });
    wx.ready(function () {
        wx.onMenuShareTimeline({//分享朋友圈
            title: shareConfig.title, // 分享标题
            link: shareConfig.link, // 分享链接
            imgUrl: shareConfig.imgUrl, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                alert('share 分享朋友圈 ok');
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });

        wx.onMenuShareAppMessage({//分享给朋友
            title: shareConfig.title, // 分享标题
            link: shareConfig.link, // 分享链接
            imgUrl: shareConfig.imgUrl, // 分享图标
            desc: shareConfig.desc, // 分享描述
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
                alert('share 朋友 ok');
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });
}

//请求网页基本信息
$.ajax({
    url:shareConfig.request,
    type: 'POST',
    data: {'url': encodeURIComponent(location.href.split('#')[0])},
    dataType: 'json',
    async: false,
    success: function (result) {
        WxCallBack(result);
    },
    timeout: 3000
})