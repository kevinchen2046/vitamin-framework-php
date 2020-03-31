var fs = require('fs');
class Util {
    /** 
     * 生成空格
     * @param len 数量
     */
    static space(len) {
        var t = [],
            i;
        for (i = 0; i < len; i++) {
            t.push(' ');
        }
        return t.join('');
    };
    /** 
     * 格式化JSON字符串
     * @param content JSON字符串
     */
    static format(content) {
        var text = content.split("\n").join(" ");
        var t = [];
        var tab = 0;
        var inString = false;
        for (var i = 0, len = text.length; i < len; i++) {
            var c = text.charAt(i);
            if (inString && c === inString) {
                // TODO: \\"
                if (text.charAt(i - 1) !== '\\') {
                    inString = false;
                }
            } else if (!inString && (c === '"' || c === "'")) {
                inString = c;
            } else if (!inString && (c === ' ' || c === "\t")) {
                c = '';
            } else if (!inString && c === ':') {
                c += ' ';
            } else if (!inString && c === ',') {
                c += "\n" + JsonUtil.space(tab * 2);
            } else if (!inString && (c === '[' || c === '{')) {
                tab++;
                c += "\n" + JsonUtil.space(tab * 2);
            } else if (!inString && (c === ']' || c === '}')) {
                tab--;
                c = "\n" + JsonUtil.space(tab * 2) + c;
            }
            t.push(c);
        }
        return t.join('')
    }

    /** 
     * 过滤文件
     * @param files 文件集合
     * @param extention 需要保留的文件扩展名
     */
    static filter(files, extention) {
        var results = [];
        for (var name of files) {
            var i = name.lastIndexOf('.');
            if (i == -1) {
                continue;
            }
            if (name.substring(i) != extention) {
                continue;
            }
            results.push(name);
        }
        return results;
    }

    /** 
     * 获取文件夹下所有文件
     * @param path 文件夹路径
     */
    static getFolderFiles(path) {
            if (!fs.existsSync(path)) return [];
            var results = [];
            var files = fs.readdirSync(path);
            for (var name of files) {
                var curPath = path + '/' + name;
                if (fs.statSync(curPath).isDirectory()) {
                    results = results.concat(this.getFolderFiles(curPath + '/'));
                } else {
                    results.push(curPath);
                }
            }
            return results;
        }
        /** 
         * 递归复制文件所有文件 到目标文件夹
         * @param fromPath 当前文件夹
         * @param toPath 目标文件夹
         */
    static copyFolder(fromPath, toPath) {
            if (!fs.existsSync(fromPath)) return;
            if (!fs.existsSync(toPath)) {
                fs.mkdirSync(toPath);
            }
            var fromFiles = this.getFolderFiles(fromPath);
            var toFiles = [];
            fromFiles.map(function(filePath) {
                toFiles.push(filePath.replace(fromPath, toPath));
            })
            for (var i = 0; i < fromFiles.length; i++) {
                var fromPath = fromFiles[i].replace(/\\/, '/').replace(/\/\//, '/').replace(/\/\//, '/');
                var toPath = toFiles[i].replace(/\\/, '/').replace(/\/\//, '/').replace(/\/\//, '/');
                var folderPath = toPath.substring(0, toPath.lastIndexOf('/'));
                if (!fs.existsSync(folderPath)) {
                    this.createFolder(folderPath);
                }
                //console.log('write:',fromPath,toPath);
                fs.writeFileSync(toPath, fs.readFileSync(fromPath));
            }
        }
        /** 
         * 复制文件 到目标文件夹
         * @param fromPath 当前文件
         * @param toPath 目标文件夹
         */
    static copyFile(filePath, toPath, newName) {
            if (!fs.existsSync(filePath) || !fs.statSync(filePath).isFile()) return;
            if (!fs.existsSync(toPath)) {
                fs.mkdirSync(toPath);
            }
            toPath = toPath.replace(/\\/, '/').replace(/\/\//, '/').replace(/\/\//, '/')
            filePath = filePath.replace(/\\/, '/').replace(/\/\//, '/').replace(/\/\//, '/')
            var index = filePath.lastIndexOf('.');
            if (index < 0 || index < (filePath.length - 6)) {
                return;
            }
            toPath = toPath.charAt(toPath.length - 1) != '/' ? (toPath + '/') : toPath;
            var name = newName ? newName : (filePath.substring(filePath.lastIndexOf('/') + 1, filePath.length));
            fs.writeFileSync(toPath + name, fs.readFileSync(filePath));
        }
        /** 
         * 创建文件夹-强制
         * @param path 文件夹路径
         */
    static createFolder(path) {
        if (!path) return;
        path = path.replace(/\\/, '/');
        path = path.replace(/\/\//, '/');
        if (path.indexOf(':')) {
            path = path.substring(path.indexOf(':') + 1, path.length);
        }
        var paths = path.split('/');
        while (true) {
            if (!paths[0]) {
                paths.shift();
                continue;
            }
            break;
        }
        var fullP = '';
        for (var p of paths) {
            fullP += p + '/';
            if (!fs.existsSync(fullP)) {
                //console.log('create:',fullP);
                fs.mkdirSync(fullP);
            }
        }
    }

    /** 
     * 删除文件夹-强制
     * @param path 文件夹路径
     */
    static removeFolder(path) {
        if (!fs.existsSync(path)) return;
        if (!fs.statSync(path).isDirectory()) {
            fs.unlinkSync(path);
            return;
        }
        var files = fs.readdirSync(path);
        for (var name of files) {
            var curPath = path + '/' + name;
            if (fs.statSync(curPath).isDirectory()) {
                this.removeFolder(curPath + '/');
            } else {
                //console.log("unlink", curPath)
                fs.unlinkSync(curPath);
            }
        }
        fs.rmdirSync(path);
    }


    static async nextTick() {
        return new Promise(function(r1, r2) {
            process.nextTick(r1);
        });
    }

    static async waitForInput(title, method) {
        title += '> ';
        process.stdout.write(title);
        //process.stdin.pause();
        process.stdin.setEncoding('utf8');

        function readHandler() {
            const chunk = process.stdin.read();
            if (chunk !== null) {
                process.stdin.on('readable', readHandler);
                method(chunk);
            }
        };
        process.stdin.on('readable', readHandler);
        // const buf = Buffer.allocUnsafe(1);
        // fs.readSync(process.stdin.fd, buf, 0, 1, 0);
        // process.stdin.end();
        // return buf.toString('utf8', 0, buf.byteLength).trim();
    }

    static async verifyTime(deviationSec, method) {
        return new Promise((reslove, reject) => {
            (new(require('./net').HttpRequest)()).request('http://api.k780.com:88/?app=life.time&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=json', (res) => {
                var serverData = JSON.parse(res);
                if (serverData.success != 1) {
                    console.error('服务器时间获取失败...'.red);
                    if (method) method(false);
                    reslove(false);
                    return;
                }
                var serverDate = new Date(serverData.result.datetime_1);
                var localDate = new Date();
                console.log('北京时间:', serverDate);
                console.log('本地时间:', localDate);
                if (Math.abs(serverDate.getSeconds() - localDate.getSeconds()) > deviationSec) {
                    console.error('时间校验失败..'.red);
                    method(false);
                    return;
                };
                console.log('时间效验成功!'.green);
                if (method) method(true);
                reslove(true);
            });
        });
    }

    static async runCmd(cmd, method) {
        return new Promise((relove, reject) => {
            var childProcess = require('child_process');
            var handler = childProcess.exec(cmd, {
                encoding: 'buffer',
                timeout: 0,
                /*子进程最长执行时间 */
                maxBuffer: 1024 * 1024
            });

            function stdotHandler(data) {
                console.log(data.toString());
            }

            function stderrHandler(data) {
                console.log(data.toString());
            }

            function exitHandler(code) {
                handler.stdout.removeListener('data', stdotHandler);
                handler.stderr.removeListener('data', stderrHandler);
                handler.removeListener('exit', exitHandler);
                if (code == 0) {
                    relove();
                    if (method) method();
                } else {
                    reject();
                }
            }
            handler.stdout.on('data', stdotHandler);
            handler.stderr.on('data', stderrHandler);
            handler.on('exit', exitHandler);
        });
    }
}

const { series } = require('gulp');

async function syncFolder() {
    Util.createFolder('../website/php');
    await Util.copyFolder('./src', '../website/php')
    return Promise.resolve();
}
exports.default = syncFolder;