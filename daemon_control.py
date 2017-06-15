#!/usr/bin/python
import json

ALLOW_COMMAND = 1
DELETE_COMMAND = 2
ALLOW_RCON = 13
DELETE_RCON = 14

def getdata():
    data = json.load(open("/srv/trashmap/srv/daemon_data.json")) 
    return data

def writefifo(order):
    line = json.dumps(order) + "\n"
    with open("/srv/trashmap/srv/daemon_input.fifo", "w") as fifo:
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
        print("suggested - List suggested rcon commands")
        print("allowed - List allowed rcon commands")
        print("allow <command> - Allow rcon command")
        print("delete <command> - Delete rcon command")
        print("exit - Exit daemon control")
    elif command == "suggested":
        data = getdata()
        for c in data["storage"]["suggested_rcon"]:
            print c
    elif command == "allowed":
        data = getdata()
        for c in data["storage"]["allowed_rcon"]:
            print c
    elif command == "allow":
        if len(args) < 1:
            print("Missing arguments")
        else:
            order = {"type":ALLOW_RCON, "command":args[0]}
            writefifo(order)
    elif command == "delete":
        if len(args) < 1:
            print("Missing arguments")
        else:
            order = {"type":DELETE_RCON, "command":args[0]}
            writefifo(order)
    elif command == "exit":
        break
    elif command:
        print("No such command")
