CC=gcc
CFLAGS=-I/usr/include/mariadb
LIBS=-lmariadb

OBJ = update-pass.o

%.o: %.c
	$(CC) -c -o $@ $< $(CFLAGS)

update-pass: $(OBJ)
	$(CC) -o $@ $^ $(CFLAGS) $(LIBS)

.PHONY: clean

clean:
	rm -f $(OBJ) update-pass
