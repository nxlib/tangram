Tangram - Module Management for PHP
========================================
## #Install
download the file [tangram-bin.zip](https://github.com/nxlib/tangram/releases/download/v3.0.0/tangram.zip)
### For windows
1. unzip the `tangram-bin.zip` to `target-path`. eg: `C:\tangram`
2. add `TANGRAM_HOME` to `environment variable`.eg:
```
TANGRAM_HOME=C:\tangram;
```
3. append `bin` path to `PATH` variable
```
PATH=...;%TANGRAM_HOME%\bin;
```

### For unix/linux/Mac OS
1. unzip the `tangram-bin.zip` to `target-path`. eg: `~/tangram`
2. append below conent to `~/.bash_profile`
```
export TANGRAM_HOME=~/tangram
export PATH=$PATH:$TANGRAM_HOME/bin
```
3. run below command
```
source ~/.bash_profile
```

## #Command
1. `tangram build`
    > create `router`、`premission` and `auth`

2. `tangram create moduleName`
    > create module which named `moduleName`

License
-------

Tangram is licensed under the GPL-3.0 License - see the [LICENSE](LICENSE) file for details
