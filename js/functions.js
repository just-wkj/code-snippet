/**
 * @author     :wkj
 * @createTime :2017/12/25 13:28
 * @description: js函数添加方便自己查找使用
 */

var justJs = {

    //是否在数组中
    in_array: function (needle, array) {
        for (var i = 0; i < array.length; i++) {
            if (array[i] == needle) {
                return true;
            }
        }
        return false;
    },

    //数组去重
    array_unique: function (array) {
        var unique = [];
        for (var i = 0; i < array.length; i++) {
            if (unique.indexOf(array[i]) < 0) {
                unique.push(array[i]);
            }
        }
        return unique;
    },

    //一维数组排序
    sort: function (array, desc) {
        var desc = desc || 0;
        return array.sort(function (a, b) {
            return desc ? b - a : a - b
        });
    },

    //二维数组排序
    ksort: function (array, key, desc) {
        var desc = desc || 0;
        return array.sort(function (a, b) {
            var x = a[key];
            var y = b[key];
            return desc ? ((x < y) ? ((x > y) ? 0 : 1) : -1) : ((x < y) ? -1 : ((x > y) ? 1 : 0));
        });
    },
	//js 时间设置
	 jsDate(time) {
			var date = new Date(parseInt(time) * 1000);
			var Y = date.getFullYear();
			var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1);
			var D = date.getDate();
			var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours());
			var m = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes());
			var s = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
			return Y + '-' + M + '-' + D + ' ' + h + ':' + m + ':' + s;
		}
}

//使用实例如下
console.log(justJs.in_array(1, ['1', 2, 3, 4]))
console.log(justJs.array_unique([1, 2, 4, 3, 2, 5, 4]));
console.log(justJs.sort([1, 2, 4, 3, 2, 5, 4]));
console.log(justJs.ksort([
    {name: 'just', age: 32},
    {name: 'wkj', age: 30},
    {name: 'moyixi', age: 21},
    {name: 'just-wkj', age: 30},
    {name: 'justwkj', age: 45}
], 'age', 0));

