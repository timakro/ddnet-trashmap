#!/usr/bin/python
import json

ALLOW_COMMAND = 1
DELETE_COMMAND = 2
ALLOW_RCON = 13
DELETE_RCON = 14

def getdata():
    data = json.load(open("/srv/trashmap/daemon_data.json")) 
    return data

def writefifo(order):
    line = json.dumps(order) + "\n"
    with open("/srv/trashmap/daemon_input.fifo", "w") as fifo:
        fifo.write(line)

print("Welcome to daemon control, enter 'help' to get a list of commands.")
while True:
    line = raw_input("> ")
    escaped = False
    string = False
    parts = [""]
    for c in line:
        if c == "\\" and not escaped:
            escaped = True
        elif c == "\"" and not escaped:
            string = not string
        elif c == " " and not string:
            if parts[len(parts)-1]:
                parts.append("")
        else:
            escaped = False
            parts[len(parts)-1] += c
    command = parts[0]
    args = parts[1:]

    if command == "help":
        print("help - Show a list of commands")
        print("msuggested - List suggested mapconfig commands")
        print("mallowed - List allowed mapconfig commands")
        print("mallow <command> <reset> - Allow mapconfig command")
        print("mdelete <command> - Delete mapconfig command")
        print("rsuggested - List suggested rcon commands")
        print("rallowed - List allowed rcon commands")
        print("rallow <command> <reset> - Allow rcon command")
        print("rdelete <command> - Delete rcon command")
        print("exit - Exit daemon control")
    elif command == "msuggested":
        data = getdata()
        for c in data["storage"]["suggested_commands"]:
            print c
    elif command == "mallowed":
        data = getdata()
        for c, r in data["storage"]["allowed_commands"].items():
            print c, repr(r)
    elif command == "mallow":
        if len(args) < 2:
            print("Missing arguments")
        else:
            try:
                reset = json.loads(args[1])
            except:
                print("Argument 'reset' not json decodeable")
            else:
                order = {"type":ALLOW_COMMAND, "command":args[0], "reset":reset}
                writefifo(order)
    elif command == "mdelete":
        if len(args) < 1:
            print("Missing arguments")
        else:
            order = {"type":DELETE_COMMAND, "command":args[0]}
            writefifo(order)
    elif command == "rsuggested":
        data = getdata()
        for c in data["storage"]["suggested_rcon"]:
            print c
    elif command == "rallowed":
        data = getdata()
        for c in data["storage"]["allowed_rcon"]:
            print c
    elif command == "rallow":
        if len(args) < 1:
            print("Missing arguments")
        else:
            order = {"type":ALLOW_RCON, "command":args[0]}
            writefifo(order)
    elif command == "rdelete":
        if len(args) < 1:
            print("Missing arguments")
        else:
            order = {"type":DELETE_RCON, "command":args[0]}
            writefifo(order)
    elif command == "exit":
        break
    elif command:
        print("No such command")
