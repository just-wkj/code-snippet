// add by wkj 2018
// email: justwkj@gmail.com
// 微信小程序常用的代码封装
// 使用时候直接 require引入如
// var Just = require('../../util/just')

const formatTime = date => {
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours()
  const minute = date.getMinutes()
  const second = date.getSeconds()

  return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':')
}

const formatNumber = n => {
  n = n.toString()
  return n[1] ? n : '0' + n
}
//======= 设置一下常用的模块
//设置页面标题
const setPageTitle = title =>{
  wx.setNavigationBarTitle({
    title
  })
}
//获取App实例
const App = getApp()
//获取接口实例
const Api = require('./api');
const Text = require('./text');
const Token = require('./token');

//toast 成功提示
const toastOk = content =>{
  wx.showToast({
    title: content,
  })
}

//toast 失败提示
const toastErr = content =>{
  wx.showToast({
    title: content,
    icon: 'none'
  })
}

const showError = (msg, callback) => {
  wx.showModal({
    title: '友情提示',
    content: msg,
    showCancel: false,
    success: function (res) {
      // callback && (setTimeout(function() {
      //   callback();
      // }, 1500));
      callback && callback();
    }
  });
}
//跳转页面
const naviagteTo = url =>{
  wx.navigateTo({
    url:url
  })
}
const redirectTo = url =>{
  wx.redirectTo({
    url:url
  })
}

//跳转tab
const switchTo = url =>{
  wx.switchTab({
    url: url
  })
}

const goBack = () => {
  wx.navigateBack()
}

const cacheGet = key => {
  return wx.getStorageSync(key);
}
const cacheSet = (key, value) => {
  return wx.setStorageSync(key, value);
}

const inArray = (key, array) =>{
  return array.indexOf(key) >= 0
}

const deleteArray = (array, key) => {
  let i = array.indexOf(key)
  array.splice(i, 1)
  return array
}

/*获取当前页url*/
const getCurrentPageUrl = () =>{
  var pages = getCurrentPages()    //获取加载的页面
  var currentPage = pages[pages.length-1]    //获取当前页面的对象
  var url = currentPage.route    //当前页面url
  return url
}

/*获取当前页带参数的url*/
const getCurrentPageUrlWithArgs = ()=>{
  var pages = getCurrentPages()    //获取加载的页面
  var currentPage = pages[pages.length-1]    //获取当前页面的对象
  var url = currentPage.route    //当前页面url
  var options = currentPage.options    //如果要获取url中所带的参数可以查看options
  //拼接url的参数
  var urlWithArgs = url + '?'
  for(var key in options){
    var value = options[key]
    urlWithArgs += key + '=' + value + '&'
  }
  urlWithArgs = urlWithArgs.substring(0, urlWithArgs.length-1)

  return urlWithArgs
}

//添加分享上报
const shareReport = (url) => {
  App._post_form(Api.shareReport, {
    url
  }, res => {
    console.log(res)
})
}

const bigImg = (url) => {
  wx.previewImage({
    current: url, // 当前显示图片的http链接
    urls: [url] // 需要预览的图片http链接列表
  })
}

const get = (url, data, success, fail, complete, check_login) => {
  wx.showNavigationBarLoading();
  // 构造请求参数
  data = data || {};

  const _this = this
  var App = getApp();
  let request = function () {
    var access_token = wx.getStorageSync('access-token');
    var user_token = wx.getStorageSync('token');

    console.log(Api.getOpenId)

    if ((App.api_root + url).indexOf(Api.getOpenId) == -1) {
      var headerParams = {
        'content-type': 'application/json',
        'access-token': access_token,
        'user-token': user_token,
        'version': 'v3.0',
        'from': 'CUSTOMER',
      }
    } else {
      var headerParams = {
        'content-type': 'application/json',
        'access-token': access_token,
        'version': 'v3.0',
        'from': 'CUSTOMER',
      }
    }


    wx.request({
      url: App.api_root + url,
      header: headerParams,
      data: data,
      success: function (res) {
        if (res.statusCode !== 200 || typeof res.data !== 'object') {
          showError('网络请求出错');
          return false;
        }
        if(res.data.code == -996){
          Token.refreshToken(function(){
            var _this = require('just');
            console.log(url)
            _this.get(url, data, success, fail, complete, check_login);
          })
        }
        if (res.data.code === -14) {
          // 登录态失效, 重新登录
          console.log('登录态失效, 重新登录')
          wx.hideNavigationBarLoading();
          console.log('登录态失效, 重新登录1')
          App.doLogin(function () {
            console.log('登录态失效, 重新登录1==重新请求')
            var _this = require('just');
            _this.get(url, data, success, fail, complete, check_login);
          });
          console.log('登录态失效, 重新登录2')
        } else if (res.data.code === 0) {
          showError(res.data.msg);
          return false;
        } else {
          success && success(res.data);
        }
      },
      fail: function (res) {
        // console.log(res);
        showError(res.errMsg, function () {
          fail && fail(res);
        });
      },
      complete: function (res) {
        wx.hideNavigationBarLoading();
        complete && complete(res);
      },
    });
  };
  // 判断是否需要验证登录
  check_login ? App.doLogin(request) : request();
}

/**
 * post提交
 */
const post = (url, data, success, fail, complete) => {
  wx.showNavigationBarLoading();
  var App = getApp();
  data = data || {};
  const _this = this

  var access_token = wx.getStorageSync('access-token');
  var user_token = wx.getStorageSync('token');

  if ((App.api_root + url).indexOf(Api.getOpenId) == -1) {
    var headerParams = {
      'content-type': 'application/x-www-form-urlencoded',
      'access-token': access_token,
      'user-token': user_token,
      'version': 'v3.0',
      'from': 'CUSTOMER',
    }
  } else {
    var headerParams = {
      'content-type': 'application/x-www-form-urlencoded',
      'access-token': access_token,
      'version': 'v3.0',
      'from': 'CUSTOMER',
    }
  }
  wx.request({
    url: App.api_root + url,
    header: headerParams,
    method: 'POST',
    data: data,
    success: function (res) {
      if (res.statusCode !== 200 || typeof res.data !== 'object') {
        showError('网络请求出错');
        return false;
      }
      if(res.data.code == -996){
        Token.refreshToken(function(){
          var _this = require('just');
          console.log(url)
          _this.post(url, data, success, fail, complete);
        })
      }
      if (res.data.code === -14) {
        wx.hideNavigationBarLoading();
        App.doLogin(function () {
          var _this = require('just');
          _this.post(url, data, success, fail, complete, check_login);
        });
        console.log('登录态失效, 重新登录2')
      } else if (res.data.code === 0) {
        showError(res.data.msg, function () {
          fail && fail(res);
        });
        return false;
      }
      success && success(res.data);
    },
    fail: function (res) {
      showError(res.errMsg, function () {
        fail && fail(res);
      });
    },
    complete: function (res) {
      wx.hideLoading();
      wx.hideNavigationBarLoading();
      complete && complete(res);
    }
  });
}

const elemetData = (e, key) => {
  return e.currentTarget.dataset[key]
}
const elementValue = e => {
  return e.detail.value
}

//判断空对象
const isEmptyObj = obj => {
  return Object.keys(obj).length === 0
}
const modal = (msg, confirm, cancel, title, showCancel) => {
  title = title || '提示';
  if(showCancel == -1){
    showCancel = false
  }
  wx.showModal({
    title: title.toString(),
    content: msg.toString(),
    showCancel,
    success(res) {
      if (res.confirm) {
        console.log('用户点击确定')
        if (typeof(confirm) == "function") {
          confirm();
        }
      } else if (res.cancel) {
        console.log('用户点击取消')
        if (typeof(cancel) == "function") {
          cancel();
        }
      }
    }
  })
}

const trimSpace = str => {
  if(str == null){
    return '';
  }
  str = str.toString()
  return str.replace(/^\s+|\s+$/g, "");
}
const timeToPercent = time => {
  time = parseInt(time)
  if (time <= 0) {
    time = 0;
  }
  var diff = time / 2;
  if (diff >= 30) {
    diff = 30;
  }
  var percent = 20 + diff;
  return `${percent}%`
}

const  download = (url) => {
  if(!url){
    return toastErr('文件不存在')
  }
  wx.downloadFile({
    url,
    success(res) {
      // 只要服务器有响应数据，就会把响应内容写入文件并进入 success 回调，业务需要自行判断是否下载到了想要的内容
      console.log(res)
      if (res.statusCode === 200) {
        wx.saveFile({
          tempFilePath: res.tempFilePath,
          success(x) {
            const savedFilePath = x.savedFilePath
            toastOk('下载成功')
          }
        })
      }
    }
  })
}

//导出设置
module.exports = {
  formatTime,
  setPageTitle,
  App,
  Api,
  Text,
  toastOk,
  toastErr,
  naviagteTo,
  redirectTo,
  switchTo,
  cacheGet,
  cacheSet,
  goBack,
  inArray,
  deleteArray,
  getCurrentPageUrl,
  getCurrentPageUrlWithArgs,
  shareReport,
  bigImg,
  post,
  get,
  showError,
  elemetData,
  elementValue,
  isEmptyObj,
  modal,
  trimSpace,
  timeToPercent,
  download
}
