request = json.load(sys.stdin)
response = handle_request(request)
print("Content-Type: application/json", end="\n\n")
json.dump(response, sys.stdout, indent=2)