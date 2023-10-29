CC = gcc
CFLAGS = -I/usr/include/postgresql
LDFLAGS = -lpq
TARGET = generate-results
SRC = generate-results.c

all: $(TARGET)

$(TARGET): $(SRC)
	$(CC) $(CFLAGS) $(SRC) -o $(TARGET) $(LDFLAGS)

clean:
	rm -f $(TARGET)

