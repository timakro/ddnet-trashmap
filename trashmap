#!/usr/bin/python
import os
import time
import json
import signal
import threading
import stat
import tempfile
import subprocess
import re
import fcntl
import traceback


CREATE_SERVER = 3
START_SERVER = 4
STOP_SERVER = 5
CHANGE_MAP = 6
CHANGE_MAPCONFIG = 7
CHANGE_PASSWORD = 8
CHANGE_RCON = 9
CHANGE_PLAYERLIMIT = 10
DELETE_SERVER = 11
SUGGEST_RCON = 12
ALLOW_RCON = 13
DELETE_RCON = 14

RUNNING = 0
SHUTDOWN = 1
ERROR = 2
EXCEPTION = 3

def mydefault(self, o):
    if isinstance(o, subprocess.Popen):
        return None
    raise TypeError(repr(o) + " is not JSON serializable")
json.JSONEncoder.default = mydefault


def handle(order):
    if order["type"] == CREATE_SERVER:
        if order["identifier"] not in data["storage"]["servers"]:
            createserver(order)
            writestorage()
    elif order["type"] == START_SERVER:
        if order["identifier"] in data["storage"]["servers"]:
            startserver(order["identifier"])
    elif order["type"] == STOP_SERVER:
        if order["identifier"] in data["storage"]["servers"]:
            stopserver(order["identifier"])
    elif order["type"] == CHANGE_MAP:
        if order["identifier"] in data["storage"]["servers"]:
            changemap(order["identifier"], order["mapfile"], order["mapname"])
            writestorage()
    elif order["type"] == CHANGE_MAPCONFIG:
        if order["identifier"] in data["storage"]["servers"]:
            if data["storage"]["servers"][order["identifier"]]["running"]:
                commands = []
                if order["mapconfig"]:
                    commands.extend(order["mapconfig"])
                commands.append(buildcommand("reload")) # reload
                writefifo(order["identifier"], "".join(commands))
            data["storage"]["servers"][order["identifier"]]["mapconfig"] = order["mapconfig"]
            writestorage()
    elif order["type"] == CHANGE_PASSWORD:
        if order["identifier"] in data["storage"]["servers"]:
            if data["storage"]["servers"][order["identifier"]]["running"]:
                writefifo(order["identifier"], buildcommand("password", order["password"] if order["password"] else ""))
            data["storage"]["servers"][order["identifier"]]["password"] = order["password"]
            writestorage()
    elif order["type"] == CHANGE_RCON:
        if order["identifier"] in data["storage"]["servers"]:
            if data["storage"]["servers"][order["identifier"]]["running"]:
                writefifo(order["identifier"], buildcommand("sv_rcon_mod_password", order["rcon"]))
            data["storage"]["servers"][order["identifier"]]["rcon"] = order["rcon"]
            writestorage()
    elif order["type"] == CHANGE_PLAYERLIMIT:
        if order["identifier"] in data["storage"]["servers"] and not data["storage"]["servers"][order["identifier"]]["running"]:
            data["storage"]["servers"][order["identifier"]]["playerlimit"] = order["playerlimit"]
            writestorage()
    elif order["type"] == DELETE_SERVER:
        if order["identifier"] in data["storage"]["servers"]:
            deleteserver(order["identifier"])
            writestorage()
    elif order["type"] == SUGGEST_RCON:
        if order["command"] not in data["storage"]["allowed_rcon"] and order["command"] not in data["storage"]["suggested_rcon"]:
            data["storage"]["suggested_rcon"].append(order["command"])
        writestorage()
    elif order["type"] == ALLOW_RCON:
        if order["command"] in data["storage"]["suggested_rcon"]:
            data["storage"]["suggested_rcon"].remove(order["command"])
        if order["command"] not in data["storage"]["allowed_rcon"]:
            data["storage"]["allowed_rcon"].append(order["command"])
        writestorage()
    elif order["type"] == DELETE_RCON:
        if order["command"] in data["storage"]["allowed_rcon"]:
            data["storage"]["allowed_rcon"].remove(order["command"])
        if order["command"] in data["storage"]["suggested_rcon"]:
            data["storage"]["suggested_rcon"].remove(order["command"])
        writestorage()


def createserver(order):
    if len(data["storage"]["servers"]) >= data["config"]["maxservers"]:
        return
    # calculate port
    ports = [i["port"] for i in data["storage"]["servers"].itervalues()]
    port = 8500
    while port in ports:
        port += 1
    # create serverdir and move map
    serverdir = os.path.join("/srv/trashmap/servers", order["identifier"])
    os.mkdir(serverdir)
    os.mkdir(os.path.join(serverdir, "maps"))
    os.rename(order["mapfile"], os.path.join(serverdir, "maps", order["mapname"]+".map"))
    # add server to memory
    data["storage"]["servers"][order["identifier"]] = {
        "label": order["label"],
        "accesskey": order["accesskey"],
        "serverdir": serverdir,
        "mapname": order["mapname"],
        "mapconfig": order["mapconfig"],
        "password": order["password"],
        "rcon": order["rcon"],
        "playerlimit": order["playerlimit"],
        "lifeseconds": 0,
        "starttime": None,
        "stoptime": 0,
        "titletime": 0,
        "running": False,
        "stopping": False,
        "process": None,
        "port": port,
        "runtimestring": None,
        "streamlast": None,
        "clientids": None
    }
    log("Created server", identifier=order["identifier"])
    # start server
    startserver(order["identifier"])


def startserver(identifier):
    if len([1 for i in data["storage"]["servers"].itervalues() if i["running"]]) >= data["config"]["maxrunningservers"]:
        return
    info = data["storage"]["servers"][identifier]
    if info["running"]:
        return
    # update memory
    info["starttime"] = info["lifeseconds"]
    info["stoptime"] = None
    info["running"] = True
    info["runtimestring"] = buildruntime(identifier)
    info["streamlast"] = ""
    info["clientids"] = []
    # format init commands
    commands = []
    commands.append(buildcommand("sv_name", buildtitle(identifier)))
    commands.append(buildcommand("sv_port", info["port"]))
    commands.append(buildcommand("sv_map", info["mapname"]))
    commands.append(buildcommand("sv_max_clients", info["playerlimit"]))
    commands.append(buildcommand("sv_rcon_mod_password", info["rcon"]))
    if info["password"]:
        commands.append(buildcommand("password", info["password"]))
    commands.append(buildcommand("exec", "banlist.cfg"))
    commands.append(buildcommand("exec", "/srv/trashmap/init.cfg"))
    for rcon in data["storage"]["allowed_rcon"]:
        commands.append(buildcommand("access_level", rcon, 2))
    if info["mapconfig"]:
        commands.extend(info["mapconfig"])
    # start server
    process = subprocess.Popen(("/srv/trashmap/DDNet-Server", "".join(commands)), cwd=info["serverdir"], stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
    fcntl.fcntl(process.stdout, fcntl.F_SETFL, os.O_RDONLY | os.O_NONBLOCK)
    info["process"] = process
    log("Started server", identifier=identifier)


def stopserver(identifier):
    info = data["storage"]["servers"][identifier]
    if not info["running"] or info["stopping"]:
        return
    # stop server
    writefifo(identifier, buildcommand("bans_save", "banlist.cfg") + buildcommand("shutdown"))
    # update memory
    info["stopping"] = True
    log("Stopped server", identifier=identifier)


def deleteserver(identifier):
    info = data["storage"]["servers"][identifier]
    # stop if still running
    if info["running"]:
        stopserver(identifier)
    # delete files
    def rmtree(path):
        if os.path.isdir(path):
            files = os.listdir(path)
            for f in files:
                rmtree(os.path.join(path, f))
            try:    os.rmdir(path)
            except: rmtree(path)
        elif os.path.exists(path):
            try:    os.remove(path)
            except: rmtree(path)
    rmtree(info["serverdir"])
    # update memory
    del data["storage"]["servers"][identifier]
    log("Deleted server", identifier=identifier)


def changemap(identifier, mapfile, mapname):
    info = data["storage"]["servers"][identifier]
    # remove old map
    os.unlink(os.path.join(info["serverdir"], "maps", info["mapname"]+".map"))
    # move new map and reload
    os.rename(mapfile, os.path.join(info["serverdir"], "maps", mapname+".map"))
    if info["running"]:
        if info["mapname"] != mapname:
            writefifo(identifier, buildcommand("change_map", mapname))
        else:
            writefifo(identifier, buildcommand("reload"))
    # update memory
    info["mapname"] = mapname
    log("Changed map", identifier=identifier)


def update(identifier):
    info = data["storage"]["servers"][identifier]
    # update stopping
    if info["stopping"]:
        running = info["process"].poll() == None if info["process"] else False
        if not running:
            info["starttime"] = None
            info["stoptime"] = info["lifeseconds"]
            info["running"] = False
            info["stopping"] = False
            info["runtimestring"] = None
            info["streamlast"] = None
            info["clientids"] = None
    # check if running
    if info["running"]:
        # check if the server has to stop
        if info["lifeseconds"]-info["starttime"] >= data["config"]["stophours"]*60*60:
            stopserver(identifier)
            return
        # update runtime
        info["runtimestring"] = buildruntime(identifier)
        # read stream
        for line in readstream(identifier):
            match = re.match("\[.*\]\[server\]: player is ready\. ClientID=([0-9]+) .*", line)
            if match:
                cid = int(match.group(1))
                if cid not in info["clientids"]:
                    info["clientids"].append(cid)
                continue
            match = re.match("\[.*\]\[server\]: client dropped\. cid=([0-9]+) .*", line)
            if match:
                cid = int(match.group(1))
                if cid in info["clientids"]:
                    info["clientids"].remove(cid)
                continue
            match = re.match("\[.*\]\[datafile\]: loading done\. .*", line)
            if match:
                if info["mapconfig"]:
                    writefifo(identifier, "".join(info["mapconfig"]))
                continue
        # check if the server has to stop
        if info["lifeseconds"]-info["starttime"] >= data["config"]["joinminutes"]*60 and len(info["clientids"]) == 0:
            stopserver(identifier)
            return
        # update title
        if info["lifeseconds"]-info["titletime"] >= data["config"]["titleupdateseconds"]:
            writefifo(identifier, buildcommand("sv_name", buildtitle(identifier)))
            info["titletime"] = info["lifeseconds"]
    else:
        # check if the server has to be deleted
        if info["lifeseconds"]-info["stoptime"] >= data["config"]["deletedays"]*24*60*60:
            deleteserver(identifier)
            return
    info["lifeseconds"] += data["config"]["tickseconds"]


def buildcommand(command, *args):
    args = ["\"" + unicode(arg).replace("\"", "\\\"") + "\"" for arg in args]
    commandstr = command + " " + " ".join(args) + ";"
    return commandstr


def buildtitle(identifier):
    info = data["storage"]["servers"][identifier]
    name = "DDNet Trashmap [" + info["label"] + "] (" + info["runtimestring"] + ")"
    return name


def buildruntime(identifier):
    info = data["storage"]["servers"][identifier]
    minutes, seconds = divmod(info["lifeseconds"]-info["starttime"], 60)
    hours, minutes = divmod(minutes, 60)
    runtime = str(hours) + "h " + str(minutes) + "m " + str(seconds) + "s"
    return runtime


def writefifo(identifier, line):
    info = data["storage"]["servers"][identifier]
    if not info["running"] or info["stopping"]:
        return
    fifo = os.path.join(data["storage"]["servers"][identifier]["serverdir"], "fifo")
    try:
        assert stat.S_ISFIFO(os.stat(fifo).st_mode)
        with open(fifo, "w+") as fifo:
            fifo.write((line+"\n").encode("utf-8"))
    except:
        log("Failed to write to server fifo", identifier=identifier, warning=True)


def readstream(identifier):
    info = data["storage"]["servers"][identifier]
    while True:
        # read rawstring
        try:
            rawstring = os.read(info["process"].stdout.fileno(), 256)
        except: break
        if not rawstring:
            break
        # form lines
        lines = rawstring.split("\n")
        lines[0] = info["streamlast"] + lines[0]
        info["streamlast"] = lines[-1]
        lines = lines[:-1]
        # yield lines
        for line in lines:
            yield line


def log(message, identifier=None, warning=False, error=False):
    prefix = ""
    if warning: prefix = "Warning: "
    elif error: prefix = "Error: "
    if identifier:
        prefix += "[" + identifier + "] "
    message = message.split("\n")
    message = [(pidstr+" "+prefix if i == 0 else "      ")+m+"\n" for i, m in enumerate(message) if m]
    try:
        logfile.write("".join(message))
        logfile.flush()
    except: pass
    if error:
        global status_flag
        status_flag = ERROR


def loadconfig(init=False):
    try:
        config = json.load(open("/srv/trashmap/daemon_config.json"))
        if init: log("Loaded config")
        else:    log("Reloaded config")
        return config
    except:
        if init: log("Failed to load config", error=True)
        else:    log("Failed to reload config", warning=True)


def loadstorage():
    try:
        storage = json.load(open("/srv/trashmap/daemon_storage.json"))
        log("Loaded storage")
        return storage
    except:
        log("Failed to load storage", error=True)


def writedata(init=False):
    try:
        tmp_file = tempfile.NamedTemporaryFile(mode="w", prefix="trashmap-", delete=False)
        json.dump(data, tmp_file, indent=4)
        tmp_file.close()
        os.chmod(tmp_file.name, 0644)
        os.rename(tmp_file.name, "/srv/trashmap/daemon_data.json")
        if init: log("Created data")
    except:
        if init: log("Failed to create data", error=True)
        else:    log("Failed to write data", warning=True)


def writestorage(final=False):
    try:
        json.dump(data["storage"], open("/srv/trashmap/daemon_storage.json", "w"), indent=4)
        if final: log("Wrote final storage")
    except:
        if final: log("Failed to write final storage", error=True)
        else:     log("Failed to write storage", warning=True)


def main():
    # open logfile
    try:
        global logfile
        logfile = open("/srv/trashmap/daemon_log.txt", "a")
    except: pass
    global pidstr
    pidstr = str(os.getpid())
    pidstr = "0" * (5 - len(pidstr)) + pidstr
    log("Trashmap daemon started")

    # create data and load files
    global data
    data = {"config":loadconfig(True), "storage":loadstorage()}
    writedata(True)

    # create and open fifo
    try:
        oldmask = os.umask(0000)
        os.mkfifo("/srv/trashmap/daemon_input.fifo", 0666)
        os.umask(oldmask)
        log("Created fifo")
    except:
        log("Failed to create fifo", warning=True)
    try:
        global fifo
        fifo = os.open("/srv/trashmap/daemon_input.fifo", os.O_RDONLY | os.O_NONBLOCK)
        assert stat.S_ISFIFO(os.fstat(fifo).st_mode)
    except:
        log("Failed to open fifo", error=True)

    # main loop
    last = ""
    while True:
        # calculate next run
        next_run = time.time() + data["config"]["tickseconds"]

        # check for config reload
        global config_reload_flag
        if config_reload_flag:
            new_config = loadconfig()
            if new_config:
                data["config"] = new_config
            config_reload_flag = False

        # check for shutdown or error
        if status_flag != RUNNING:
            shutdown()
            break

        # handle fifo 
        while True:
            try:
                rawstring = os.read(fifo, 256)
            except: break
            if not rawstring:
                break

            lines = rawstring.split("\n")
            lines[0] = last + lines[0]
            last = lines[-1]
            lines = lines[:-1]

            for line in lines:
                try:
                    order = json.loads(line)
                except:
                    log("Failed to json decode fifo input", error=True)
                else:
                    handle(order)

        # update servers
        for identifier in data["storage"]["servers"].keys():
            update(identifier)

        # write data to file
        writedata()

        # sleep for a tick
        delta = next_run - time.time()
        if delta < 0:
            log("Skipped {0} ticks".format(delta / -data["config"]["tickseconds"]), warning=True)
        else:
            time.sleep(delta)


def shutdown():
    # stop servers
    try:
        for identifier in data["storage"]["servers"].keys():
            stopserver(identifier)
    except: pass

    # close and remove fifo
    try:
        os.close(fifo)
        os.unlink("/srv/trashmap/daemon_input.fifo")
        log("Removed fifo")
    except:
        log("Failed to remove fifo", warning=True)

    # remove data
    try:
        os.unlink("/srv/trashmap/daemon_data.json")
        log("Removed data")
    except:
        log("Failed to remove data", warning=True)

    # for safety write storage a last time
    try:
        if data["storage"]:
            writestorage(True)
    except: pass

    # close logfile
    if status_flag == SHUTDOWN:    log("Trashmap daemon terminated")
    elif status_flag == ERROR:     log("Trashmap daemon terminated because an error occurred")
    elif status_flag == EXCEPTION: log("Trashmap daemon terminated because a python exception occurred")
    try:
        logfile.close()
    except: pass



# start thread
status_flag = RUNNING
config_reload_flag = False
def starter():
    try:
        main()
    except:
        global status_flag
        status_flag = EXCEPTION
        log(traceback.format_exc())
        shutdown()
        raise
thread = threading.Thread(target=starter)
thread.start()

# set up sigterm handler
def handle_sigterm(x, y):
    global status_flag
    status_flag = SHUTDOWN
signal.signal(signal.SIGTERM, handle_sigterm)

# set up sigusr1 handler
def handle_sighup(x, y):
    global config_reload_flag
    config_reload_flag = True
signal.signal(signal.SIGHUP, handle_sighup)

# wait for shutdown
try:
    while True:
        if status_flag != RUNNING:
            break
        time.sleep(1)
except KeyboardInterrupt:
    status_flag = SHUTDOWN

# wait for the thread to finish
thread.join()
