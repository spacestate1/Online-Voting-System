CC = gcc
CFLAGS = -Wall -Wextra -g
LIBS = -lpq
INCLUDE_DIRS = -I/usr/include/postgresql

all: action_items_vote

action_items_vote: action_items_vote.c
	$(CC) $(CFLAGS) $(INCLUDE_DIRS) -o $@ $< $(LIBS)

clean:
	rm -f action_items_vote

.PHONY: all clean

